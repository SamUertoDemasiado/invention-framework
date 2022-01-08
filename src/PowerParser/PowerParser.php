<?php


namespace OSN\Framework\PowerParser;


use OSN\Framework\Core\App;

class PowerParser extends ParseData
{
    protected string $tmpdir;
    protected string $file;

    public function __construct(string $file)
    {
        $this->tmpdir = tmp_dir();
        $this->file = $file;
    }

    protected function php(string $code): string
    {
        return "<?php $code ?>";
    }

    protected function replaceFromPregArray(string $str, array $arr, string $repl)
    {
        $out = $str;

        if (isset($arr[0][0]) && isset($arr[1][0])) {
            foreach ($arr[0] as $k => $value) {
                $out = str_replace($value, str_replace('%s', $arr[1][$k], $repl), $out);
            }
        }

        return $out;
    }

    protected function endblock(string $code, string $regex)
    {
        return preg_replace("/$regex/", $this->php("}\n"), $code);
    }

    public function replaceDirectivesWithPHPCode(string $code): string
    {
        $output = $code;
        $matches = [];

        /**
         * If, elseif, else and endif statements.
         */
        $regex = ":if\((.*)\)";
        if (preg_match_all("/$regex/", $output, $matches)) {
            $output = $this->replaceFromPregArray($output, $matches, $this->php('if (%s) {'));
        }

        $regex = ":elseif\((.*)\)";
        if (preg_match_all("/$regex/", $output, $matches)) {
            $output = $this->replaceFromPregArray($output, $matches, $this->php("}\nelseif (%s) {"));
        }

        $regex = ":else";
        if (preg_match_all("/$regex/", $output, $matches)) {
            $output = preg_replace("/$regex/", $this->php("}\nelse {"), $output);
        }

        $output = $this->endblock($output, ':endif');

        /**
         * foreach and endforeach statements.
         * This must be parsed before for loop parsing.
         */
        $regex = ":foreach\((.*)\)";
        if (preg_match_all("/$regex/", $output, $matches)) {
            $output = $this->replaceFromPregArray($output, $matches, $this->php('foreach (%s) {'));
        }

        $output = $this->endblock($output, ':endforeach');

        /**
         * for and endfor statements.
         */
        $regex = ":for\((.*)\)";
        if (preg_match_all("/$regex/", $output, $matches)) {
            $output = $this->replaceFromPregArray($output, $matches, $this->php('for (%s) {'));
        }

        $output = $this->endblock($output, ':endfor');

        /**
         * php and endphp statements.
         */
        $output = preg_replace('/:php/', "<?php\n", $output);
        $output = preg_replace('/:endphp/', "\n?>", $output);

        /**
         * while and endwhile statements.
         */
        $regex = ":while\((.*)\)";
        if (preg_match_all("/$regex/", $output, $matches)) {
            $output = $this->replaceFromPregArray($output, $matches, $this->php('while (%s) {'));
        }

        $output = $this->endblock($output, ':endwhile');

        /**
         * dowhile and enddowhile statements.
         */
        $output = preg_replace('/:dowhile/', $this->php("do {\n"), $output);

        $regex = ":enddowhile\((.*)\)";
        if (preg_match_all("/$regex/", $output, $matches)) {
            $output = $this->replaceFromPregArray($output, $matches, $this->php("}\nwhile (%s);"));
        }

        /**
         * title statement.
         */

        $regex = ":title\((.*)\)";
        if (preg_match_all("/$regex/", $output, $matches)) {
            $output = $this->replaceFromPregArray($output, $matches, $this->php("\$title = %s;"));
        }

        /**
         * section and endsection statements.
         */
        $regex = ":section\((.*)\)";
        if (preg_match_all("/$regex/", $output, $matches)) {
            $output = $this->replaceFromPregArray($output, $matches, $this->php('$_names[] = %s; $_names_modified[] = %s; ob_start();'));
        }

        $output = preg_replace('/:endsection/', $this->php('$_sections[array_shift($_names_modified)] = ob_get_clean();'), $output);

        /**
         * getSection statement.
         */
        $regex = ":getSection\((.*)\)";
        if (preg_match_all("/$regex/", $output, $matches)) {
            $output = $this->replaceFromPregArray($output, $matches, $this->php('echo $_sections[%s] ?? "";'));
        }

        /**
         * extends statement.
         */
        $regex = ":extends\((.*)\)";
        if (preg_match_all("/$regex/", $output, $matches)) {
            $output = $this->replaceFromPregArray($output, $matches, $this->php('$_layout = %s;'));
        }

        return $output;
    }

    public function parse(string $code): string
    {
        $replacements = $this->replacements();
        $output = $code;

        foreach ($replacements as $str => $replacement) {
            $output = preg_replace("/$str/", $replacement, $output);
        }

        return $this->replaceDirectivesWithPHPCode($output);
    }

    public function compile(): string
    {
        $content = file_get_contents($this->file);
        return $this->parse($content);
    }

    public function __invoke(): array
    {
        $compiled = $this->compile();
        $tmpfile = $this->tmpdir . rand() . '_' . basename($this->file);
        file_put_contents($tmpfile, $compiled);

        return ["file" => $tmpfile];
    }
}
