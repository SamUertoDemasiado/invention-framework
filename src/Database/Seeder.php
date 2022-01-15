<?php


namespace OSN\Framework\Database;


use App\Commands\Console\DBCommand;
use OSN\Framework\Core\Database;

abstract class Seeder
{
    protected Database $db;

    abstract public function execute(Database $db);

    /**
     * Seeder constructor.
     */
    public function __construct()
    {
        $this->db = db();
    }

    public function seed()
    {
        return $this->execute($this->db);
    }

    protected function call(array $seeders)
    {
        foreach ($seeders as $seeder) {
            DBCommand::seedOne($seeder);
        }
    }
}
