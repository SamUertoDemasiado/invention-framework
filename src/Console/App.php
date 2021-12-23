<?php


namespace OSN\Framework\Console;


use OSN\Envoy\Exception;
use OSN\Envoy\ParseENV;
use OSN\Framework\Core\Database;

class App
{
    public array $config = [];
    public Arguments $argument;
    public Generator $generator;
    public Commander $commander;
    public Database $db;
    public static self $app;
    public array $env = [];

    /**
     * @throws Exception
     */
    public function __construct(string $root_dir)
    {
        $this->config['root_dir'] = $root_dir;
   //     $this->config['commands_dir'] = $root_dir . '/app/Commands';
    //    $this->config['commands_namespace'] = '\App\Commands';
        $this->env = (new ParseENV())->parseFile($this->config["root_dir"] . "/.env");
        $this->db = new Database($this->env);
        $this->argument = new Arguments();
        $this->generator = new Generator();
        $this->commander = new Commander($this->argument);
        self::$app = $this;
    }

    public static function config($key)
    {
        return self::$app->config[$key] ?? false;
    }

    public static function env(): array
    {
        return self::$app->env;
    }

    public static function db(): Database
    {
        return self::$app->db;
    }

    public function run()
    {
        if (!isCLI()) {
            echo 'This script must be invoked with PHP CLI.\n';
            exit(-1);
        }

        try {
            echo $this->commander->runCommand();
        }
        catch (\Exception $e) {
            echo $e->getMessage();
            exit($e->getCode());
        }
    }
}
