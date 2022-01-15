<?php


namespace OSN\Framework\Console;


class Command
{
    public array $argsRequired = [];
    public string $commandString;
    protected bool $subcommandRequired = false;

    public function subcommandsDescription(): array
    {
        return [];
    }

    public function requireArgument(string $method, int $count = 0)
    {
        $this->argsRequired[$method]['count'] = $count;
    }

    public function requireArgumentMultiple(array $methods, int $count = 0)
    {
        foreach ($methods as $method) {
            $this->requireArgument($method, $count);
        }
    }

    public function requireOption(string $method, string $option)
    {
        $this->argsRequired[$method]['options'][] = $option;
    }

    public function commandNS(): string
    {
        $array = explode("\\", static::class);
        return strtolower(preg_replace("/Command/", '', end($array)));
    }

    public function subcommandRequired()
    {
        $this->subcommandRequired = true;
    }

    public function default(ArgumentCollection $args)
    {
        $c = "[:subcommand]";

        if ($this->subcommandRequired) {
            $c = ":<subcommand>";
        }

        if ($args->hasOption('--help')) {
            echo("Usage: php {$args->_0} " . $this->commandNS() . "$c [options...]\n\n");
            echo("Options:\n");
            echo("  \033[1;33m--help\033[0m\tShow this help and exit\n\n");
            echo("Available Subcommands:\n");
            echo($this->renderSubcommandsList());
            return false;
        }

        return true;
    }

    public function getScriptName()
    {
        global $argv;
        return $argv[0];
    }

    public function getActions(): array
    {
        $methods0 = get_class_methods(static::class);
        $excluded = get_class_methods(self::class);

        $methods = array_filter($methods0, function ($value) use ($excluded) {
            return !in_array($value, $excluded) && substr($value, 0, 2) !== '__' && $value !== 'default';
        });

        asort($methods);

        return $methods;
    }

    public function renderSubcommandsList(): string
    {
        $subcmds = $this->subcommandsDescription();
        $out = '';

        $methods = $this->getActions();
        $methods = array_merge(!method_exists($this, 'default') || !isset($subcmds['default']) ? [] : ['default'], $methods);

        $classCmd = $this->commandString;

        foreach ($methods as $method) {
            $out .= "\t\033[1;32m{$classCmd}";

            if ($method != 'default') {
                $out .= ":{$method}";
            }

            $out .= "\033[0m\t\t";

            if (isset($subcmds[$method][0])) {
                $out .= $subcmds[$method][0];
            }

            $out .= "\n";
            $out .= "\t     Usage:\n";
            $out .= "\t        \033[1;32mphp\033[1;33m {$this->getScriptName()} \033[0m\033[1m{$classCmd}";

            if ($method != 'default') {
                $out .= ":{$method}";
            }

            $out .= "\033[0m";

            if (isset($subcmds[$method][2])) {
                $out .=  ' ' . $subcmds[$method][2];
            }

            $out .= "\n";

            if (isset($subcmds[$method][1])) {
                $out .= "\t     Options:\n";
                $options = $subcmds[$method][1];

                foreach ($options as $option => $desc) {
                    $out .= "\t        \033[1;33m$option\033[0m\t\t$desc\n";
                }

                $out .= "\n";
            }
        }

        return $out;
    }

    public function run(string $method, array $args = [])
    {
        $args = array_merge([null, null], $args);
        return call_user_func_array([$this, $method], [new ArgumentCollection($args)]);
    }

    public function runForeign(string $cmdclass, string $method = 'default', $args = [])
    {
        $args = is_array($args) ? array_merge([null, null], $args) : $args;
        return call_user_func_array([new $cmdclass(), $method], [is_array($args) ? new ArgumentCollection($args) : $args]);
    }

    public function error($msg, $code)
    {
        echo $msg;
        exit($code);
    }
}
