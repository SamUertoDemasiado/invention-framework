<?php


namespace OSN\Framework\Database\Common;


use OSN\Framework\Console\App;
use OSN\Framework\Database\MySQL\Column as MySQLColumn;
use OSN\Framework\Database\SQLite\Column as SQLiteColumn;

abstract class Blueprint
{
    protected string $table;
    protected string $sqlStart = 'CREATE TABLE {{ table }}(';
    protected string $sqlEnd = ');';

    /**
     * @var array<SQLiteColumn, MySQLColumn>
     */
    protected array $columns = [];

    /**
     * Blueprint constructor.
     * @param string $table
     */
    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function __toString()
    {
        return $this->getSQL();
    }

    public function __invoke(): string
    {
        return $this->getSQL();
    }

    public function getSQL(): string
    {
        $sqlMain = '';

        foreach ($this->columns as $column) {
            $sqlMain .= "\n" . $column() . ",";
        }

        $sqlMain = $this->sqlStart . substr($sqlMain, 0, strlen($sqlMain) - 1) . "\n" . $this->sqlEnd;
        $sqlMain = str_replace("{{ table }}", "{$this->table}", $sqlMain);

        return "{$sqlMain}";
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function add(string $type, string $column, string $attrs = '', bool $colname = true): Column
    {
        if(App::db()->getVendor() === 'sqlite')
            $col = new SQLiteColumn($column);
        else
            $col = new MySQLColumn($column);

        $col->append(" $type $attrs", $colname);

        $this->columns[] = $col;

        return $col;
    }

    public function renderLength($length): string
    {
        return $length === 0 ? '' : "($length)";
    }

    public function renderType(string $type, int $length = 0): string
    {
        return "$type" . $this->renderLength($length);
    }

    public function int(string $column, int $length = 0): Column
    {
        return $this->add($this->renderType("INTEGER", $length), $column);
    }

    public function string(string $column, int $length = 0): Column
    {
        return $this->add($this->renderType("VARCHAR", $length === 0 ? 255 : $length), $column);
    }

    public function text(string $column): Column
    {
        return $this->add($this->renderType("TEXT"), $column);
    }

    public function timestamp(string $column): Column
    {
        return $this->add($this->renderType("TIMESTAMP"), $column);
    }

    public function date(string $column): Column
    {
        return $this->add($this->renderType("DATE"), $column);
    }

    public function time(string $column): Column
    {
        return $this->add($this->renderType("TIME"), $column);
    }

    public function datetime(string $column): Column
    {
        return $this->add($this->renderType("DATETIME"), $column);
    }

    public function foreignKey(string $column, string $reference_table, string $reference_column): Column
    {
        return $this->add("FOREIGN KEY ($column) REFERENCES {$reference_table}($reference_column)", '', '', false);
    }

    public function timestamps(string $column = '')
    {
        $cols = ['created_at', 'updated_at'];

        if ($column == 'created_at')
            $cols = ["created_at"];
        elseif ($column == 'updated_at')
            $cols = ["updated_at"];

        foreach ($cols as $col) {
            $this->timestamp($col);
        }
    }
}
