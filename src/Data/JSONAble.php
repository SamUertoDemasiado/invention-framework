<?php


namespace OSN\Framework\Data;


trait JSONAble
{
    public function toJSON()
    {
        $data = $this->rawData();
        return json_encode($this->toArray($data), JSON_PRETTY_PRINT);
    }

    public function __toString()
    {
        return $this->toJSON();
    }
}
