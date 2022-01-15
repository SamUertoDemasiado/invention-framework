<?php


namespace OSN\Framework\ORM\Relationships;


use Closure;
use OSN\Framework\Core\Database;
use OSN\Framework\Core\Model;
use OSN\Framework\Database\Query;
use OSN\Framework\ORM\DualRelationship;
use OSN\Framework\ORM\Relationship;

class HasOne extends DualRelationship
{
    protected function makeQuery()
    {
        return $this->query
            ->select($this->relationalModel->table)
            ->where($this->relationalModel->table . '.' . $this->tableToForeignColumn($this->baseModel->table), $this->baseModel->get($this->baseModel->primaryColumn));
    }

    public function last()
    {
        $this->query->orderBy($this->relationalModel->table . '.' . $this->relationalModel->primaryColumn, true);
        return $this;
    }

    public function first()
    {
        $this->query->orderBy($this->relationalModel->table . '.' . $this->relationalModel->primaryColumn, false);
        return $this;
    }

    public function get()
    {
        $data = parent::get();
        return $data->hasGet(0);
    }
}
