<?php


namespace OSN\Framework\Files;


class File
{
    protected string $name;

    public function __construct(string $file)
    {
        $this->name = $file;
    }

    public function exists(bool $relative = true): bool
    {
        $prefix = $relative ? app()->config('root_dir') : '';
        return file_exists($prefix . '/' . $this->name);
    }
}
