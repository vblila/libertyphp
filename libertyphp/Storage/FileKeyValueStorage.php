<?php

namespace Libertyphp\Storage;

class FileKeyValueStorage implements KeyValueStorageInterface
{
    /** @var string */
    private $path;

    /**
     * @param string $key
     * @return string
     */
    protected function getFileSource($key)
    {
        return "{$this->path}/{$key}";
    }

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function store(string $key, $value): bool
    {
        if (!file_exists($this->path)) {
            mkdir($this->path, 0777, true);
        }

        $fileResource = fopen($this->getFileSource($key), 'w');

        $result = fwrite($fileResource, serialize($value));
        fclose($fileResource);

        return $result !== false;
    }

    public function load(string $key)
    {
        $fileSource = $this->getFileSource($key);
        if (!file_exists($fileSource)) {
            return null;
        }

        $storedData = file_get_contents($fileSource);

        return unserialize($storedData);
    }

    public function delete(string $key): bool
    {
        $fileSource = $this->getFileSource($key);
        if (file_exists($fileSource)) {
            return unlink($fileSource);
        }

        return false;
    }
}
