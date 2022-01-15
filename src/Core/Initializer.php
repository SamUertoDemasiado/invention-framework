<?php


namespace OSN\Framework\Core;


abstract class Initializer
{
    protected $app;

    abstract public function init();
    abstract public function preinit();
    abstract public function afterinit();

    /**
     * @param App|\OSN\Framework\Console\App $app
     */
    public function setApp($app)
    {
        $this->app = $app;
    }
}
