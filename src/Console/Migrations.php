<?php

namespace OSN\Framework\Console;

use Closure;
use OSN\Framework\Core\Database;
use OSN\Framework\Core\Migration;
use PDO;

class Migrations
{
    protected string $path;
    protected array $migrations = [];
    protected Database $db;

    public function __construct()
    {
        $this->path = App::$app->config["root_dir"] . "/database/migrations/";
        $this->migrations = scandir($this->path);
        $this->db = App::db();
    }

    public function applyAll()
    {
        asort($this->migrations);
        foreach ($this->migrations as $migrationFile) {
            if (is_dir($migrationFile))
                continue;

            $migrationClass = explode(".", $migrationFile)[0];

            echo "Applying migration: $migrationClass\n";

            include_once $this->path . $migrationFile;

            /** @var Migration $migration */
            $migration = new $migrationClass();

            if($migration->up($this->db) === false) {
                echo "[!]: Migration is already up: $migrationClass\n";
                continue;
            }

            echo "Applied migration: $migrationClass\n";
        }
    }

    public function rollbackAll()
    {
        rsort($this->migrations);
        foreach ($this->migrations as $migrationFile) {
            if (is_dir($migrationFile))
                continue;

            $migrationClass = explode(".", $migrationFile)[0];
            echo "Rolling back migration: $migrationClass\n";

            include_once $this->path . $migrationFile;

            $migration = new $migrationClass();

            if($migration->down($this->db) === false) {
                echo "[!]: Migration is not applied: $migrationClass\n";
                continue;
            }

            echo "Rolled back migration: $migrationClass\n";
        }
    }
}
