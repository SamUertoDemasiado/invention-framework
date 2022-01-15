<?php

namespace OSN\Framework\Utils\Security;

/**
 * @method static sha1(false|mixed $post)
 */
class Hash
{
    public function __call($method, $args)
    {
        return hash($method, $args[0]);
    }
}
