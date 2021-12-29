<?php


namespace OSN\Framework\Data;


use OSN\Framework\Core\Collection;
use OSN\Framework\Core\Model;

trait ArrayAble
{
    function toArray($data): array
    {
        $out = [];

        foreach ($data as $key => $value) {
            if (($value instanceof Collection || $value instanceof Model) && method_exists($value, 'rawData')) {
                $out[$key] = toJSON($value->rawData());
            }
            elseif (is_array($value) || is_object($value)) {
                $out[$key] = toJSON($value);
            }
            else {
                $out[$key] = $value;
            }
        }

        return $out;
    }
}
