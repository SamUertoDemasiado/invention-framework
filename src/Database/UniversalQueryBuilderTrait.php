<?php


namespace OSN\Framework\Database;


trait UniversalQueryBuilderTrait
{
    public function __call($name, $arguments)
    {
        if (method_exists($this->query, $name)) {
            call_user_func_array([$this->query, $name], $arguments);
            return $this;
        }

        throw new MethodNotFoundException('Call to undefined method ' . static::class . '::' . $name . '()');
    }
}
