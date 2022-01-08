<?php


namespace OSN\Framework\Core;


use JsonSerializable;
use OSN\Framework\Exceptions\CollectionException;

class Collection implements JsonSerializable
{
    use CollectionArrayMethods;

    protected array $array;

    /**
     * Collection constructor.
     * @param array $array
     */
    public function __construct(array $array)
    {
        $newArray = [];

        foreach ($array as $key => $value) {
            $newArray[$key] = $value;
        }

        $this->array = $newArray;
    }

    /**
     * @throws CollectionException
     */
    public function get($index = null)
    {
        if ($index === null) {
            return $this->array;
        }

        if (!isset($this->array[$index])) {
            throw new CollectionException("Collection key '{$index}' doesn't exist");
        }

        return $this->array[$index];
    }

    public function _($index)
    {
        return $this->get($index);
    }

    public function __get($key)
    {
        $index = $key;

        if ($key[0] === '_')
            $index = substr($key, 1);

        return $this->_($index);
    }

    public function __set($key, $value)
    {
        $index = $key;

        if (is_string($key) && $key[0] === '_')
            $index = substr($key, 1);

        $this->array[$key] = $value;
    }

    public function set($key, $value)
    {
        $this->__set($key, $value);
    }

    public function jsonSerialize(): array
    {
       return $this->array;
    }

    public function __invoke(): array
    {
        return $this->array;
    }

    public function has($key): bool
    {
        try {
            $tmp = $this->get($key);
            return true;
        }
        catch (CollectionException $e) {
            return false;
        }
    }

    public function hasGet($key)
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        return null;
    }
}
