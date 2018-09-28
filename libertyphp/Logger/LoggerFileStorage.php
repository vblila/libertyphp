<?php

namespace Libertyphp\Logger;

use Libertyphp\Storage\KeyValueStorageInterface;
use Exception;

class LoggerFileStorage implements KeyValueStorageInterface
{
    /** @var string */
    private $source;

    /**
     * @param string $source
     */
    public function __construct($source)
    {
        $this->source = $source;
    }

    public function store($key, $value)
    {
        $dir = dirname($this->source);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $fileResource = file_exists($this->source)
            ? fopen($this->source, 'a+')
            : fopen($this->source, 'w');

        $pid = getmypid();

        fwrite($fileResource, "{$key} {$pid} {$value}" . PHP_EOL);
        fclose($fileResource);
    }

    public function load($key)
    {
        throw new Exception('Logger file storage not implements load method');
    }

    public function delete($key)
    {
        throw new Exception('Logger file storage not implements delete method');
    }
}
