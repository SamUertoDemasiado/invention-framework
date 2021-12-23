<?php


namespace OSN\Framework\Database\SQLite;

use \OSN\Framework\Database\Common\Column as CommonColumn;

class Column extends CommonColumn
{

    public function autoIncrement(): self
    {
        return $this->append(" AUTOINCREMENT", false);
    }

    public function primaryKey(): self
    {
        $name = $this->column;

        $keywords = explode(' ', $this->colSQL);

        foreach ($keywords as $key => $keyword) {
            if ($keyword === $name) {
                continue;
            }

            if (trim($keyword) == '') {
                continue;
            }

            $keywords[$key] = " {$keyword} PRIMARY KEY ";
            break;
        }

        $this->colSQL = implode(' ', $keywords);

        return $this;
    }
}