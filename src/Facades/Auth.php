<?php

namespace OSN\Framework\Facades;

use OSN\Framework\Core\Facade;

class Auth extends Facade
{
    protected static string $className = \App\Utils\Auth::class;
}