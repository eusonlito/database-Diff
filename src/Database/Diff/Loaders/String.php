<?php namespace Database\Diff\Loaders;

use Exception;

class String implements LoaderInterface
{
	static public function load($string)
	{
		if (!is_string($string)) {
			throw new Exception('String Loader only allow strings');
		}

		return trim($string);
	}
}