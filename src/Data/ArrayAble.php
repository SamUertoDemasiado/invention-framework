<?php


namespace OSN\Framework\Data;


use OSN\Framework\Core\Collection;
use OSN\Framework\Core\Model;

trait ArrayAble
{
    public function toArray($data)
    {
        $out = [];

        foreach ($data as $key => $value) {
            if (($value instanceof Collection || $value instanceof Model) && method_exists($value, 'rawData')) {
                $out[$key] = $this->toArray($value->rawData());
            }
            elseif (is_array($value) || is_object($value)) {
                $out[$key] = $this->toArray($value);
            }
            else {
                $out[$key] = $value;
            }
        }

        return is_object($data) ? (object) $out : $out;
    }
}
