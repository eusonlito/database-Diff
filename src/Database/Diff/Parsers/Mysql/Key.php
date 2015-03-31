<?php namespace Database\Diff\Parsers\Mysql;

class Key
{
    private $type;
    private $name;
    private $foreign_table;
    private $foreign_column;
    private $on_delete;

    public static function getParseAlterExp()
    {
        return '/ALTER TABLE `?([a-zA-Z0-9$\_,]+[^`\s])`?'
            .'\s*(ADD|DROP)\s*(?:INDEX)?\s*'
            .'`?([a-z0-9$\_,]+[^`\s])`?'
            .'\s*`?([a-z0-9_-]+)`?\s*(\(`?([a-z0-9_-]+)`?\))?\s*;/i';
    }

    public static function getAll($sql)
    {
        return [];
    }
}
