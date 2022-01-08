<?php


namespace OSN\Framework\Database;


use OSN\Framework\Core\Collection;
use OSN\Framework\Core\Database;
use OSN\Framework\Core\Model;

class Table
{
    use TableQueryTrait;

    public function __construct(string $tableName = '', string $primaryKey = 'id', string $model = '')
    {
        $this->db = db();
        $this->query = new Query($tableName, $model);
        $this->primaryKey = $primaryKey;

        if ($tableName !== null) {
            $this->setTableName($tableName);
        }
    }
}
