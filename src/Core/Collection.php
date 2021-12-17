<?php


namespace OSN\Framework\Core;


use OSN\Framework\Exceptions\CollectionException;
use OSN\Framework\Extras\CollectionArrayMethods;

class Collection
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
    public function _($index)
    {
        if (!isset($this->array[$index])) {
            throw new CollectionException("Collection key '{$index}' doesn't exist");
        }

        return $this->array[$index];
    }

    public function __get($key)
    {
        $index = $key;

        if ($key[0] === '_')
            $index = substr($key, 1);

        return $this->_($index);
    }

    public function __toString()
    {
        ob_start();
        print_r($this->array);
        return ob_get_clean();
    }

    public function __invoke(): array
    {
        return $this->array;
    }

    public function search(string $regexp, bool $index = false): array
    {
        $out = [];

        foreach ($this->array as $key => $item) {
            if (is_numeric($item) || $item === 0) {
                $item = $item . '';
            }

            if (is_string($item) && preg_match($regexp, $item)) {
                if ($index)
                    $out[$index] = $item;
                else
                    $out[] = $item;
            }
        }

        return $out;
    }
}