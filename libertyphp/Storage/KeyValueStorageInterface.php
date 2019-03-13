<?php

namespace Libertyphp\Storage;

interface KeyValueStorageInterface
{
    /**
     * @param string $key
     * @param mixed $value
     *
     * @return bool
     */
    public function store(string $key, $value): bool;

    /**
     * @param string $key
     * @return mixed|null
     */
    public function load(string $key);

    /**
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool;
}
