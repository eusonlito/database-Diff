<?php namespace Database\Diff;

use Exception;

class DD
{
	private $databases = [];

	public function loadFile($driver, $file)
	{
		return $this->load('file', $driver, $file);
	}

	public function loadString($driver, $string)
	{
		return $this->load('string', $driver, $string);
	}

	public function loadDatabase($data)
	{
		if (empty($data['driver'])) {
			throw new Exception('"driver" parameter is required');
		}

		return $this->load('database', $data['driver'], $data);
	}

	public function load($method, $driver, $data)
	{
		if (count($this->databases) >= 2) {
			throw new Exception('Only two databases can be loaded');
		}

		$sql = Loaders\LoaderFactory::getLoader($method, $data);
		$parser = Parsers\ParserFactory::getParser($driver, $sql);

		return $this->databases[] = $parser;
	}

	public function diff()
	{
		if (count($this->databases) !== 2) {
			throw new Exception('You need two databases loaded');
		}
	}
}
