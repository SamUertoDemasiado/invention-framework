<?php


namespace OSN\Framework\Core;


use Error;
use Exception;
use JsonSerializable;
use OSN\Framework\Database\Query;
use OSN\Framework\Database\QueryBuilderTrait;
use OSN\Framework\Database\Table;
use OSN\Framework\Database\TableQueryTrait;
use OSN\Framework\Exceptions\ModelException;
use OSN\Framework\Exceptions\PropertyNotFoundException;
use OSN\Framework\ORM\ORMBaseTrait;
use PDO;


/**
 * @method select(array|string $data = [])
 * @method patch()
 * @method insert()
 * @method delete()
 * @method truncate()
 */
abstract class Model implements JsonSerializable
{
    use ORMBaseTrait;

    protected array $data = [];
    protected array $fillable = [];
    protected array $guarded = [];

    public ?string $table = null;
    public string $primaryColumn = 'id';
    public Table $_table;
    protected Database $db;

    public function __construct(?array $data = null)
    {
        if ($data != null)
            $this->load($data);

        $this->db = App::db();

        if($this->table === null) {
            $array = explode('\\', get_class($this));
            $this->table = strtolower(end($array)) . 's';
        }

        $this->guarded[] = $this->primaryColumn;
        $this->_table = new Table($this->table, $this->primaryColumn, static::class);
    }

    protected function db(): Database
    {
        return $this->db;
    }

    public function get($key = true)
    {
        if ($key === true)
            return $this->data;

        return $this->data[$key] ?? false;
    }

    /**
     * @throws ModelException
     */
    public function load(array $data)
    {
        foreach ($data as $key => $value) {
            if (!$this->isFillable($key))
                throw new ModelException("The field '" . static::class . "::$key' is not fillable");

            $this->data[$key] = $value;
        }
    }

    /**
     * @throws PropertyNotFoundException
     */
    public function __get($name)
    {
        $data = $this->get($name);

        if ($data === false) {
            throw new PropertyNotFoundException('Cannot find the specified property.');
        }

        return $data;
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function isFillable($field): bool
    {
        if (in_array($field, $this->fillable) && !in_array($field, $this->guarded)) {
            return true;
        }

        return false;
    }

    public function isGuarded($field): bool
    {
        if (in_array($field, $this->guarded) && !in_array($field, $this->fillable)) {
            return true;
        }

        return false;
    }

    public function jsonSerialize(): array
    {
        return $this->data;
    }

    /**
     * @param $primaryValue
     * @return Model|Collection
     * @throws \OSN\Framework\Exceptions\CollectionException
     */
    public static function find($primaryValue)
    {
        $model = new static();

        $data = $model->_table->select()->where($model->_table->primaryKey, $primaryValue)->get()->hasGet(0);

        if ($data === null)
            return null;

        foreach ($data as $k => $item) {
            $model->{$k} = $item;
        }

        return $model;
    }

    /*
     * The CRUD Methods (MASS ASSIGNMENT).
     */

    /**
     * @throws ModelException
     */
    public static function all(): Collection
    {
        $models = collection();

        try {
            $tmp = new static();
            $data = $tmp->db->queryFetch("SELECT * FROM " . $tmp->table);

            foreach ($data as $datum) {
                $model = new static();

                foreach ($datum as $field => $value) {
                    $model->$field = $value;
                }

                $models->push($model);
            }

            return $models;
        }
        catch (Exception $e) {
            throw new ModelException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @throws ModelException
     * @return Model|Collection
     */
    public static function create(array $data)
    {
        if (!isset($data[0])) {
            $data = [$data];
        }

        $models = collection();

        foreach ($data as $datum) {
            $model = new static();
            $model->load($datum);
            $model->insert()->execute();
            $models->push($model);
        }

        return $models->count() < 2 ? $models->_0 : $models;
    }

    /**
     * @throws ModelException
     */
    public static function update(array $data)
    {
        $model = new static();
        $primaryValue = $data[$model->primaryColumn] ?? false;
        $patchedData = $data;

        if ($primaryValue !== false) {
            unset($patchedData[$model->primaryColumn]);
        }

        $model->load($patchedData);
        $model->patch()->where($model->primaryColumn, $primaryValue)->execute();
        $model->{$model->primaryColumn} = $primaryValue;

        return $model;
    }

    /**
     * @throws ModelException
     */
    public static function destroy(int $primaryValue): self
    {
        $model = new static();
        $model->_table->delete()->where($model->primaryColumn, $primaryValue)->execute();

        return $model;
    }

    protected static function isCUD($name): bool
    {
        if ($name === 'insert' || $name === 'patch' || $name === 'delete')
            return true;

        return false;
    }

    public function save()
    {
        return $this->insert()->execute();
    }

    public function __call($name, $args)
    {
        if (self::isCUD($name)) {
            return call_user_func_array([$this->_table, $name], array_merge([$this->data], $args));
        }

        return call_user_func_array([$this->_table, $name], $args);
    }

    public static function __callStatic($name, $args)
    {
        $obj = new static();

        if (self::isCUD($name)) {
            return call_user_func_array([$obj->_table, $name], array_merge([$obj->data], $args));
        }

        return call_user_func_array([$obj->_table, $name], $args);
    }
}
