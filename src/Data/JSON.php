<?php


namespace OSN\Framework\Data;


class JSON
{
    use ArrayAble, JSONAble;
    private static $data;

    public function rawData()
    {
        return self::$data;
    }

    public static function convert($data)
    {
        self::$data = $data;
        $_this = new self();
        return $_this->toJSON($data);
    }
}
