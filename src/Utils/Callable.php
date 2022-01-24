<?php

namespace OSN\Framework\Utils;

/**
 * Makes an object invokable or callable.
 *
 * @author smalldev2 <smalldev2@onesoftnet.ml>
 * @package OSN\Framework\Utils
 */
interface Callable
{
    /**
     * The invoke magic method.
     *
     * @return mixed
     */
    public function __invoke();
}
