<?php

namespace OSN\Framework\Facades;

use OSN\Framework\Core\Facade;

class CSRF extends Facade
{
    protected static string $className = \OSN\Framework\Utils\Security\CSRF::class;
}
