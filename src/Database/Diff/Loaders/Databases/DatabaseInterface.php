<?php namespace Database\Diff\Loaders\Databases;

use Exception;
use PDO;

abstract class DatabaseInterface
{
	protected static $connection;
    protected static $factory;

	static protected function connect(array $data)
	{
		if (empty($data['driver']) || empty($data['database']) || empty($data['username'])) {
			throw new Exception('"driver", "database" and "username" are required');
		}

		$dsn = $data['driver'].':dbname='.$data['database'];

		if (isset($data['host'])) {
			$dsn .= ';host='.$data['host'];
		}

		if (empty($data['password'])) {
			$data['password'] = null;
		}

		self::$connection = new PDO($dsn, $data['username'], $data['password'], [
  			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  		]);

  		return self::$connection;
	}

	static protected function query($query)
	{
		$q = self::$connection->query($query);

        $result = $q->fetchAll(PDO::FETCH_ASSOC);

        $q->closeCursor();

        return $result;
	}
}