<?php


namespace OSN\Framework\Database;


use Closure;
use OSN\Framework\Console\App;
use OSN\Framework\Core\Database;
use OSN\Framework\Database\MySQL\Blueprint as MySQLBlueprint;
use OSN\Framework\Database\SQLite\Blueprint as SQLiteBlueprint;

/**
 * Class Schema
 * @package App\Console
 * @todo Add createIfNotExists() method
 */
class Schema
{
    protected Database $db;
    public static string $query;

    /**
     * @param string $query
     */
    public function setQuery(string $query): void
    {
        self::$query = $query;
    }

    /**
     * DatabaseSchema constructor.
     */
    public function __construct()
    {
        $this->db = App::db();
    }

    public function create(string $table, Closure $callback)
    {
        if ($this->db->getVendor() === 'sqlite')
            $blueprint = new SQLiteBlueprint($table);
        else
            $blueprint = new MySQLBlueprint($table);

        call_user_func_array($callback, [$blueprint]);
        $this->setQuery($blueprint . '');
        return $blueprint;
    }

    public function drop(string $table): string
    {
        $query = "DROP TABLE $table;";
        $this->setQuery($query);
        return $query;
    }

    public function dropIfExists(string $table): string
    {
        $query = "DROP TABLE IF EXISTS $table";
        $this->setQuery($query);
        return $query;
    }
}
