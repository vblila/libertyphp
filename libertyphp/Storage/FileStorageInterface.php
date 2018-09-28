<?php

namespace Libertyphp\Storage;

interface FileStorageInterface
{
    /**
     * @param string $fileKey
     * @return string
     */
    public function getFileUrl($fileKey);

    /**
     * Сохраняет файл из fileSource в хранилище, возвращает ключ файла в хранилище
     * @param string $fileSource
     * @return string
     */
    public function save($fileSource);

    /**
     * Сохраняет контент в файле (существующем или новом), возвращает ключ файла в хранилище
     *
     * @param string $content
     * @param string|null $fileKey
     *
     * @return string
     */
    public function saveContent($content, $fileKey = null);

    /**
     * @param string $fileKey
     * @return string
     */
    public function getContent($fileKey);
}
