<?php namespace Database\Diff\Loaders\Databases;

use Exception;

class Mysql extends DatabaseInterface
{
    public function __construct(array $data)
    {
        return self::connect($data);
    }

    public function getTables()
    {
        $sql = '';

        foreach (self::query('SHOW TABLES') as $table) {
            $q = self::query('SHOW CREATE TABLE `'.array_values($table)[0].'`');
            $sql .= "\n".$q[0]['Create Table'].';';
        }

        return trim($sql);
    }
}