<?php


namespace OSN\Framework\Console;


use Closure;
use OSN\Framework\Core\Collection;
use OSN\Framework\Exceptions\ArgumentException;
use OSN\Framework\Exceptions\CommandNotFoundException;

class Commander
{
    /** @var Command[] */
    public array $commands = [];
    public Arguments $arguments;
    public ?Command $default = null;

    /**
     * Commander constructor.
     * @param Arguments $arguments
     */
    public function __construct(Arguments $arguments)
    {
        $this->arguments = $arguments;
    }

    public function register(string $cmd, string $command)
    {
        $command = new $command();
        $command->commandString = $cmd;
        $this->commands[$cmd] = $command;
    }

    public function registerClosure($cmd, Closure $closure)
    {
        $this->commands[$cmd] = $closure;
    }

    public function getClassFromUserCmd($cmd): array
    {
        $arr = explode(':', $cmd);
        $class = $arr[0];
        $method = $arr[1] ?? 'default';

        return ['class' => $class, 'method' => $method];
    }

    /**
     * @throws ArgumentException
     * @throws CommandNotFoundException
     */
    public function runCommand()
    {
        global $argv;
        $userCmd = $this->arguments->get(1);

        if ($userCmd === null) {
            throw new ArgumentException("No argument given.");
        }

        $class = $this->getClassFromUserCmd($userCmd);
        $cmd = $this->commands[$class['class']] ?? false;

        if ($cmd === false || !method_exists($cmd, $class['method'])) {
            if ($this->default !== null) {
                $out = $this->default->default(new ArgumentCollection(app()->argv));

                if ($out != '' && is_string($out)) {
                    if (substr($out, strlen($out) - 2) != "\n")
                        $out .= "\n";

                    return $out;
                }

                return "\n";
            }
            else {
                throw new CommandNotFoundException('The command "' . $userCmd . "\" could not be found.");
            }
        }

        $filtered_argv = array_filter($argv, function ($value) {
            return $value[0] !== '-';
        });

        if (($cmd->argsRequired[$class['method']]['count'] ?? 0) > (count($filtered_argv) - 2)) {
            throw new ArgumentException("The command \"$userCmd\" requires {$cmd->argsRequired[$class['method']]['count']} argument(s), " . (count($filtered_argv) - 2) . " passed");
        }

        $optsRequired = $cmd->argsRequired[$class['method']]['options'] ?? [];
        $args = new ArgumentCollection($argv);

        foreach ($optsRequired as $optRequired) {
            if ($args->optionHasValue($optRequired) === false) {
                throw new ArgumentException("The command \"$userCmd\" requires the '" . $optRequired . "' option");
            }
        }

        $out = call_user_func_array([$cmd, $class['method']], [$args]);

        if (!is_string($out)) {
            $out = '';
        }

        if (substr($out, strlen($out) - 2) != "\n") {
            $out .= "\n";
        }

        return $out;
    }

    public function renderCommandList()
    {
        foreach ($this->commands as $command => $object) {
            dump($command, $object);
        }
    }

    public function registerDefault(string $cmd)
    {
        $this->default = new $cmd();
    }
}
