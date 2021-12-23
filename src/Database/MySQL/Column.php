<?php


namespace OSN\Framework\Database\MySQL;

use \OSN\Framework\Database\Common\Column as CommonColumn;

class Column extends CommonColumn
{
    public function autoIncrement(): self
    {
        return $this->append(" AUTO_INCREMENT", false);
    }
}