<?php namespace Database\Diff\Loaders\Databases;

use Exception;

class DatabaseFactory
{
    public static function getDatabase(array $data)
    {
        if (empty($data['driver'])) {
            throw new Exception('"driver" parameter is required');
        }

        $class = __NAMESPACE__.'\\'.ucfirst($data['driver']);

        if (!class_exists($class)) {
            throw new Exception(sprintf('Loader %s is not available', $method));
        }

        return new $class($data);
    }
}