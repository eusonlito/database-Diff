<?php namespace Database\Diff\Parsers;

use Exception;

class ParserFactory
{
	public static function getParser($driver, $sql)
	{
		$class = __NAMESPACE__.'\\'.ucfirst($driver);

		if (!class_exists($class)) {
			throw new Exception(sprintf('Parser %s is not available', $driver));
		}

		return $class::load($sql);
	}
}
