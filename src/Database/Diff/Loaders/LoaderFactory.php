<?php namespace Database\Diff\Loaders;

use Exception;

class LoaderFactory
{
	public static function getLoader($method, $data)
	{
		$class = __NAMESPACE__.'\\'.ucfirst($method);

		if (!class_exists($class)) {
			throw new Exception(sprintf('Loader %s is not available', $method));
		}

		return $class::load($data);
	}
}