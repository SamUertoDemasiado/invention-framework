<?php


namespace OSN\Framework\Utils;


use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * Class FunctionUtils
 * @package OSN\Framework\Utils
 */
class FunctionUtils
{
    /**
     * @var object|string
     */
    protected $objectOrMethod;
    protected ?string $method;
    protected bool $associative;

    /**
     * FunctionUtils constructor.
     * @param $objectOrMethod
     * @param string|null $method
     * @param bool $associative
     */
    public function __construct($objectOrMethod, string $method = null, bool $associative = false)
    {
        $this->objectOrMethod = $objectOrMethod;
        $this->method = $method;
        $this->associative = $associative;
    }

    /**
     * Code help taken from here <https://stackoverflow.com/questions/70411469/is-there-any-way-to-find-argument-type-list-of-a-function-in-php/70411741#70411741>.
     *
     * @throws ReflectionException
     * @see https://stackoverflow.com/questions/70411469/is-there-any-way-to-find-argument-type-list-of-a-function-in-php/70411741#70411741
     * @author JMP
     * @author Ar Rakin <rakinar2@gmail.com>
     */
    public function getParameterTypes(): array
    {
        $attr = (new ReflectionMethod($this->objectOrMethod, $this->method));

        $attr = $attr->getParameters();

        $params = [];

        foreach ($attr as $att) {
            $type = $att->getType();
            $typeName = $type === null ? 'mixed' : $type->getName();

            if ($this->associative) {
                $params[$att->name] = $typeName;
            }
            else {
                $params[] = $typeName;
            }
        }

        return $params;
    }
}
