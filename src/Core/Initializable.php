<?php


namespace OSN\Framework\Core;


trait Initializable
{
    protected array $initializers = [];

    public function loadInitializers()
    {
        foreach (config('initializers') as $value) {
            $this->initializers[] = new $value();
        }
    }

    public function preinit()
    {
        foreach ($this->initializers as $initializer) {
            $initializer->setApp(app());
            $initializer->preinit();
        }
    }

    public function init()
    {
        foreach ($this->initializers as $initializer) {
            $initializer->setApp(app());
            $initializer->init();
        }
    }

    public function afterinit()
    {
        foreach ($this->initializers as $initializer) {
            $initializer->setApp(app());
            $initializer->afterinit();
        }
    }
}
