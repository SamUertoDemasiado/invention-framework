<?php


namespace OSN\Framework\ORM;


use Closure;
use OSN\Framework\Database\Query;
use OSN\Framework\Database\UniversalQueryBuilderTrait;
use OSN\Framework\Exceptions\MethodNotFoundException;

abstract class Relationship
{
    use UniversalQueryBuilderTrait;

    protected Query $query;

    abstract protected function makeQuery();

    public function __construct()
    {
        $this->query = new Query();
        $this->makeQuery();
    }

    public function get()
    {
        return $this->query->get();
    }

    protected function tableToForeignColumn(string $table, string $append = '_id')
    {
        return preg_replace('/s$/', $append, $table);
    }

    public function custom(Closure $callback): self
    {
        call_user_func_array($callback, [$this->query]);
        return $this;
    }

    public function getQuery(): string
    {
        return $this->query->getQuery();
    }
}
