<?php


namespace OSN\Framework\Core;


use OSN\Framework\Database\DatabaseVendors;
use PDO;

class Database
{
    public ?PDO $pdo;
    public string $dsn;
    public string $dbname;
    public array $env;

    public function __construct($env)
    {
        $this->env = $env;

        $vendor = $this->getVendor();
        $dsn = $vendor . ":";

        if ($vendor === 'mysql') {
            $dsn .= 'host=' . $env["DB_HOST"] . ';port=' . $env["DB_PORT"] . ';dbname=' . $env["DB_NAME"];
            $this->pdo = new PDO($dsn, $env['DB_USER'], $env["DB_PASSWORD"]);
        }
        elseif ($vendor === 'sqlite') {
            $dsn .= $env["DB_NAME"];
            $this->pdo = new PDO($dsn);
        }

        $this->dsn = $dsn;
        $this->dbname = $env["DB_NAME"];
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getVendor(): ?string
    {
        if (in_array($this->env['DB_VENDOR'], DatabaseVendors::$vendors)) {
            return $this->env['DB_VENDOR'];
        }

        return null;
    }

    public function chooseQuery(array $queries)
    {
        foreach ($queries as $vendor => $query) {
            if ($vendor == $this->getVendor()) {
                return $query;
            }
        }
    }

    public function query($sql)
    {
        return $this->pdo->query($sql);
    }

    public function queryFetch($sql, $params = null): array
    {
        return $this->pdo->query($sql)->fetchAll($params !== null ? $params : PDO::FETCH_ASSOC);
    }

    public function prepare($sql)
    {
        return $this->pdo->prepare($sql);
    }

    public function __destruct()
    {
        $this->pdo = null;
    }
}
