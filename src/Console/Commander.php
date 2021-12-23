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

    /**
     * Commander constructor.
     * @param Arguments $arguments
     */
    public function __construct(Arguments $arguments)
    {
        $this->arguments = $arguments;
    }

    public function register(string $cmd)
    {
        $array = explode('\\', $cmd);
        $userCmd = strtolower(str_replace('Command', '', end($array)));
        $this->commands[$userCmd] = new $cmd();
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
            throw new CommandNotFoundException('The command "' . $userCmd . "\" could not be found.");
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

        return call_user_func_array([$cmd, $class['method']], [$args]);
    }
}
