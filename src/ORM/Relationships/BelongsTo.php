<?php


namespace OSN\Framework\ORM\Relationships;


use OSN\Framework\Core\Database;
use OSN\Framework\Core\Model;
use OSN\Framework\Database\Query;
use OSN\Framework\ORM\DualRelationship;
use OSN\Framework\ORM\Relationship;

class BelongsTo extends DualRelationship
{
    protected function makeQuery()
    {
        return $this->query
            ->select($this->relationalModel->table)
            ->where($this->relationalModel->table . '.' . $this->relationalModel->primaryColumn, $this->baseModel->get($this->tableToForeignColumn($this->relationalModel->table)));
    }

    public function get()
    {
        $data = parent::get();
        return $data->hasGet(0);
    }
}
