<?php namespace Database\Diff\Parsers;

class Mysql implements ParserInterface
{
    static private $tables = [];

    public static function load($sql)
    {
        return (new self)->fromSql($sql);
    }

    private function fromSql($sql) 
    {
        self::$tables = Mysql\Table::getAll($sql);
        dd(self::$tables);
    }
}