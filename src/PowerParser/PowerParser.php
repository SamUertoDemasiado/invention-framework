<?php


namespace OSN\Framework\PowerParser;


use OSN\Framework\Core\App;

class PowerParser extends ParseData
{
    protected string $tmpdir;
    protected string $file;

    public function __construct(string $file)
    {
        $this->tmpdir = App::config("root_dir") . "/tmp/";
        $this->file = $file;
    }

    public function parse(string $code)
    {
        $replacements = $this->replacements();
        $output = $code;

        foreach ($replacements as $str => $replacement) {
            $output = preg_replace("/$str/", $replacement, $output);
        }

        return $output;
    }

    public function compile()
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
