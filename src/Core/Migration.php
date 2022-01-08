<?php


namespace OSN\Framework\Core;


use OSN\Framework\Database\Schema;
use OSN\Framework\Database\MySQL\Blueprint;

abstract class Migration
{
    protected string $migrationName;
    protected bool $entryLogging = true;

    abstract public function safeUp();
    abstract public function safeDown();

    public function __construct()
    {
        $this->migrationName = get_class($this);
    }

    public function registerMigration($pdo)
    {
        $timestamp = date("Y-m-d H:i:s");

        $stmt = $pdo->prepare("INSERT INTO migrations(name, created_at) VALUES(:name, :created_at);");
        $stmt->execute(["name" => $this->migrationName, "created_at" => $timestamp]);
    }

    public function unregisterMigration($pdo)
    {
        $stmt = $pdo->prepare("DELETE FROM migrations WHERE name = :name");
        $stmt->execute(["name" => $this->migrationName]);
    }

    /**
     * @param $db
     * @return bool
     * @todo Update code
     */
    public function isApplied($db): bool
    {
        $tables = $db->pdo->query($db->chooseQuery([
            'sqlite' => "SELECT name FROM sqlite_master WHERE type = 'table'",
            'mysql' => "SELECT TABLE_NAME AS name
                        FROM INFORMATION_SCHEMA.TABLES
                        WHERE TABLE_SCHEMA = '" . \OSN\Framework\Console\App::$app->db->dbname . "'"
        ]))->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($tables as $table) {
            if ($table["name"] == 'migrations') {
                $migrations = $db->pdo->query("SELECT * FROM migrations WHERE name = '{$this->migrationName}'")->fetchAll(\PDO::FETCH_ASSOC);

                if (count($migrations) > 0) {
                    return true;
                }

                break;
            }
        }

        return false;
    }

    public function up($db): bool
    {
        if (!$this->isApplied($db)){
            $q = $this->safeUp();

            if ($this->entryLogging)
                $this->registerMigration($db->pdo);

            return true;
        }

        return false;
    }

    public function down($db): bool
    {
        if ($this->isApplied($db)) {
            if ($this->entryLogging)
                $this->unregisterMigration($db->pdo);

            $q = $this->safeDown();

            return true;
        }

        return false;
    }
}
