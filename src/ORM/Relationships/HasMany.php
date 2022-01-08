<?php


namespace OSN\Framework\ORM\Relationships;


use OSN\Framework\Core\Collection;
use OSN\Framework\Core\Model;
use OSN\Framework\Database\Query;
use OSN\Framework\ORM\DualRelationship;
use OSN\Framework\ORM\Relationship;

class HasMany extends DualRelationship
{
    protected function makeQuery()
    {
        return $this->query
            ->select($this->relationalModel->table)
            ->where($this->relationalModel->table . '.' . $this->tableToForeignColumn($this->baseModel->table), $this->baseModel->get($this->baseModel->primaryColumn));
    }
}
