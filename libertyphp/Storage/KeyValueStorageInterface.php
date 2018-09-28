<?php

namespace Libertyphp\Storage;

interface KeyValueStorageInterface
{
    /**
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function store($key, $value);

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public function load($key);

    /**
     * @param string $key
     *
     * @return void
     */
    public function delete($key);
}
