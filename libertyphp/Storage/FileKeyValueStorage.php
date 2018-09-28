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

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    public function store($key, $value)
    {
        if (!file_exists($this->path)) {
            mkdir($this->path, 0777, true);
        }

        $fileResource = fopen($this->getFileSource($key), 'w');

        fwrite($fileResource, serialize($value));
        fclose($fileResource);
    }

    public function load($key)
    {
        $fileSource = $this->getFileSource($key);
        if (!file_exists($fileSource)) {
            return null;
        }

        $storedData = file_get_contents($fileSource);

        return unserialize($storedData);
    }

    public function delete($key)
    {
        $fileSource = $this->getFileSource($key);
        if (file_exists($fileSource)) {
            unlink($fileSource);
        }
    }
}
