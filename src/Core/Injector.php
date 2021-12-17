<?php


namespace OSN\Framework\Core;


class Injector
{
    protected function renderHTML(string $html, ...$args): string
    {
        return sprintf($html, ...$args);
    }
}