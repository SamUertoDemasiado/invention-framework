<?php


namespace OSN\Framework\Database;


use Error;
use OSN\Framework\Core\Collection;
use OSN\Framework\Core\Database;

trait TableQueryTrait
{
    protected ?string $tableName;
    public string $primaryKey = 'id';
    protected Database $db;
    public Query $query;


    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @param string $tableName
     */
    public function setTableName(string $tableName): void
    {
        $this->tableName = $tableName;
    }

    public function all(): Collection
    {
        return $this->query->all($this->getTableName());
    }

    public function insert(array $data)
    {
        return $this->query->insert($this->getTableName(), $data);
    }

    public function patch(array $data)
    {
        return $this->query->update($this->getTableName(), $data);
    }

    public function select(array $data = ['*'])
    {
        return $this->query->select($this->getTableName(), $data);
    }

    public function delete()
    {
        return $this->query->delete($this->getTableName());
    }

    public function truncate()
    {
        return $this->query->truncate($this->getTableName());
    }

    public function __call($name, $args)
    {
        if (method_exists($this->query, $name)) {
            return call_user_func_array([$this->query, $name], $args);
        }

        throw new Error("Call to undefined method " . get_class($this) . '::' . $name . '()');
    }

    public static function __callStatic($name, $args)
    {
        $obj = new static();
        if (method_exists($obj->query, $name)) {
            return call_user_func_array([$obj->query, $name], $args);
        }

        throw new Error("Call to undefined method " . get_class($obj) . '::' . $name . '()');
    }
}
