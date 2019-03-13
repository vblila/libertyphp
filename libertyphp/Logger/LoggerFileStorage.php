<?php

namespace Libertyphp\Logger;

use Libertyphp\Storage\KeyValueStorageInterface;
use Exception;

class LoggerFileStorage implements KeyValueStorageInterface
{
    /** @var string */
    private $source;

    public function __construct(string $source)
    {
        $this->source = $source;
    }

    public function store(string $key, $value): bool
    {
        $dir = dirname($this->source);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $fileResource = file_exists($this->source)
            ? fopen($this->source, 'a+')
            : fopen($this->source, 'w');

        $pid = getmypid();

        $result = fwrite($fileResource, "{$key} {$pid} {$value}" . PHP_EOL);
        fclose($fileResource);

        return $result !== false;
    }

    public function load(string $key)
    {
        throw new Exception('Logger file storage not implements load method');
    }

    public function delete(string $key): bool
    {
        throw new Exception('Logger file storage not implements delete method');
    }
}
