<?php


namespace OSN\Framework\Console;


use OSN\Envoy\Exception;
use OSN\Envoy\ParseENV;
use OSN\Framework\Core\Collection;
use OSN\Framework\Core\Config;
use OSN\Framework\Core\Database;
use OSN\Framework\Core\Initializable;
use OSN\Framework\Exceptions\CommandNotFoundException;

class App
{
    use Initializable;

    public Config $config;
    public Arguments $argument;
    public Generator $generator;
    public Commander $commander;
    public Database $db;
    public static self $app;
    public array $env = [];
    public array $argv;

    /**
     * @throws Exception
     */
    public function __construct(string $root_dir)
    {
        global $argv;

        $this->env = (new ParseENV())->parseFile($root_dir . "/.env");
        self::$app = $this;
        $this->config = new Config($root_dir . '/' . $this->env['CONF_DIR']);
        $this->config->root_dir = $root_dir;
        self::$app = $this;

        $this->loadInitializers();
        $this->preinit();

        $this->argv = $argv;
        $this->db = new Database($this->env);
        $this->argument = new Arguments();
        $this->generator = new Generator();
        $this->commander = new Commander($this->argument);
        self::$app = $this;
        $this->init();
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
            echo 'This application must be invoked with PHP CLI.\n';
            exit(-1);
        }

        try {
            $this->afterinit();
            echo $this->commander->runCommand();
        }
        catch (\Throwable $e) {
            echo "\033[1;31m" . get_class($e) . "\033[0m: " . $e->getMessage() . " \033[1;33m(Code " . $e->getCode() . ")\033[0m\n";
            echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
            exit($e->getCode());
        }
    }
}
