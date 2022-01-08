<?php


namespace OSN\Framework\ORM;


use OSN\Framework\Database\Query;

abstract class Relationship
{
    protected Query $query;

    abstract protected function makeQuery();

    public function __construct()
    {
        $this->query = new Query();
    }

    public function get()
    {
        return $this->makeQuery()->get();
    }

    protected function tableToForeignColumn(string $table, string $append = '_id')
    {
        return preg_replace('/s$/', $append, $table);
    }
}
