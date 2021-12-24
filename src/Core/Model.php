<?php


namespace OSN\Framework\Core;


use Exception;
use OSN\Framework\Exceptions\ModelException;
use OSN\Framework\Exceptions\PropertyNotFoundException;

abstract class Model
{
    protected array $data = [];
    protected array $fillable = [];
    protected array $guarded = [];

    protected static Database $db;
    protected static ?string $table = null;
    protected static string $primaryColumn = 'id';

    public function __construct(?array $data = null)
    {
        if ($data != null)
            $this->load($data);

        self::$db = App::db();

        if(static::$table === null) {
            $array = explode('\\', get_class($this));
            static::$table = strtolower(end($array)) . 's';
        }

        $this->guarded[] = static::$primaryColumn;
    }

    protected function db(): Database
    {
        return App::db();
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

    /*
     * The CRUD Methods.
     */

    /**
     * @throws ModelException
     */
    public function insert(): self
    {
        $values = [];

        foreach ($this->data as $key => $value) {
            $values[] = $value;
        }

        $keys = implode(', ', array_keys($this->data));
        $placeholders = implode(',', array_map(function ($value) {
            return '?';
        }, $this->data));

        try {
            $statement = self::$db->prepare("INSERT INTO " . static::$table . "($keys) VALUES($placeholders)");
            $statement->execute($values);
        }
        catch (Exception $e) {
            throw new ModelException($e->getMessage(), $e->getCode());
        }

        return $this;
    }

    /**
     * @throws ModelException
     */
    public function patch(): self
    {
        $values = [];

        foreach ($this->data as $key => $value) {
            if ($key === static::$primaryColumn) {
                $values['primaryValue'] = $this->{$key};
                continue;
            }

            $values[] = $value;
        }

        $keys = array_keys($this->data);
        $queryPart = '';

        foreach ($keys as $key) {
            if ($key === static::$primaryColumn) {
                continue;
            }

            $queryPart .= " $key = ?,";
        }

        $queryPart = substr($queryPart, 0, strlen($queryPart) - 1);

        try {
            $sql = "UPDATE " . static::$table . " SET $queryPart";

           if (isset($values["primaryValue"])) {
                $sql .= " WHERE " . static::$primaryColumn . " = :primaryValue";
           }

            $statement = self::$db->prepare($sql);
            $statement->execute($values);
        }
       catch (Exception $e) {
           throw new ModelException($e->getMessage(), $e->getCode());
       }

        return $this;
    }

    /**
     * @throws ModelException
     */
    public function destroy()
    {
        $values = [];

        if (isset($this->data[static::$primaryColumn])) {
            $values['primaryValue'] = $this->data[static::$primaryColumn];
        }

        if (!isset($values['primaryValue']))
            throw new ModelException("A primary column value is required to delete individual rows");

        $sql = "DELETE FROM " . static::$table . ' WHERE ' . static::$primaryColumn . " = :primaryValue";

        try {
            $statement = self::$db->prepare($sql);
            $statement->execute($values);
            return $this;
        }
        catch (Exception $e) {
            throw new ModelException($e->getMessage(), $e->getCode());
        }
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
            new static();
            $data = self::$db->queryFetch("SELECT * FROM " . static::$table);

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
            $model->insert();
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
        $primaryValue = $data[static::$primaryColumn] ?? false;
        $patchedData = $data;

        if ($primaryValue !== false) {
            $model->{static::$primaryColumn} = $primaryValue;
            unset($patchedData[static::$primaryColumn]);
        }

        $model->load($patchedData);
        $model->patch();

        return $model;
    }

    /**
     * @throws ModelException
     */
    public static function delete(int $primaryValue): self
    {
        $model = new static();

        $model->{static::$primaryColumn} = $primaryValue;
        $model->destroy();

        return $model;
    }
}
