<?php namespace Database\Diff\Parsers\Mysql;

class Table
{
    private $name;
    private $sql = [];
    private $columns = [];
    private $keys = [];
    private $params = [];

    public static function getParseExp()
    {
        return '/CREATE TABLE (?:IF NOT EXISTS )?'
            .'`?([a-zA-Z0-9$\_,]+[^`\s])`?'
            .'\s*\(([a-zA-Z0-9\s\(\)`,\_\'\.:-]+)\)\s*'
            .'([\sa-zA-Z0-9=_]+);/i';
    }

    public static function parse($sql)
    {
        preg_match(self::getParseExp(), trim(str_replace(["\n", "\r"], '', $sql)), $m);

        return $m;
    }

    public static function getAll($sql)
    {
        $tables = [];

        preg_match_all(self::getParseExp(), $sql, $m);

        foreach ($m[0] as $i => $row) {
            $tables[$m[1][$i]] = new self($row);
        }

        preg_match_all(Column::getParseAlterExp(), $sql, $m);
dd($m);
        foreach ($m[0] as $i => $row) {
            $tables[$m[1][$i]]->{$m[2][$i].'ColumnFromSql'}($row);
        }

        return $tables;
    }

    public function __construct($sql)
    {
        $this->sql = self::parse($sql);
        $this->name = $this->sql[1];
        $this->columns = Column::getAll($this->sql[2]);
        $this->keys = Key::getAll($this->sql[2]);

        $this->setParameters();

        return $this;
    }

    public function addKeyFromSql($sql)
    {
        preg_match('/`?([a-z0-9_-]+)`?\s*(\(`?([a-z0-9_-]+)`?\))?/i', $sql, $m);


        if (stristr($row, $alter.'add')) {
        }
    }

    private function setParameters()
    {
        $params = $this->sql[3];

        if (preg_match('/engine\s*=\s*([a-z]+)/i', $params, $m)) {
            $this->params['engine'] = $m[1];
        }

        if (preg_match('/default character set\s*=?\s*([a-z0-9]+)/i', $params, $m)) {
            $this->params['charset'] = $m[1];
        } elseif (preg_match('/default charset\s*=?\s*([a-z0-9]+)/i', $params, $m)) {
            $this->params['charset'] = $m[1];
        }

        if (preg_match('/collate\s*=?\s*([a-z0-9_]+)/i', $params, $m)) {
            $this->params['collate'] = $m[1];
        }
    }

    public function __toString()
    {
        return 'CREATE TABLE `'.$this->name.'` ('
            .' '.implode(', ', $this->columns)
            .', '.implode(', ', $this->keys)
            .') '.$this->params;
    }
}