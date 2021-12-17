<?php


namespace OSN\Framework\PowerParser;


abstract class ParseData
{
    protected function replacements(): array
    {
        return [
            "{{" => '<?=',
            "}}" => '?>',
        ];
    }
}