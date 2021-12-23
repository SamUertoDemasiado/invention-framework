<?php


namespace OSN\Framework\Console;


class Generator
{
    public function generate($path, $template, ...$args)
    {
        $template = $this->getTemplate($template);
        $content = sprintf($template, ...$args);

        if (file_exists($path)) {
            echo "[!] Cannot generate '".basename($path)."': File exists\n";
            exit(-1);
        }

        file_put_contents($path, $content);
    }

    public function getTemplate($t)
    {
        $file = App::config('root_dir') . "/resources/templates/{$t}.template";
        return file_get_contents($file);
    }
}
