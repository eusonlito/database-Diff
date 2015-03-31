<?php namespace Database\Diff\Loaders;

use Exception;

class File implements LoaderInterface
{
	static public function load($file)
	{
		if (!is_file($file)) {
			throw new Exception(sprintf('File %s not exists', $file));
		}

		return trim(file_get_contents($file));
	}
}