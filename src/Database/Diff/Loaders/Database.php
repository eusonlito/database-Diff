<?php namespace Database\Diff\Loaders;

use Exception;

class Database extends Databases\DatabaseInterface
{
	static public function load($data)
	{
        self::$factory = Databases\DatabaseFactory::getDatabase($data);

        return self::$factory->getTables();
	}
}