<?php

namespace OSN\Framework\Facades;

use OSN\Framework\Core\Facade;

class Hash extends Facade
{
    protected static string $className = \App\Utils\Hash::class;
}