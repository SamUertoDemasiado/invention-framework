<?php


namespace OSN\Framework\Database;


class Query
{
    use QueryBuilderTrait;

    public function __construct(string $currentTable = '', string $model = '')
    {
        $this->db = db();
        $this->currentTable = $currentTable;
        $this->model = $model;
    }

    public function __toString()
    {
        return $this->getQuery();
    }
}
