<?php

namespace Libertyphp\Storage;

interface FileStorageInterface
{
    public function getFileUrl(string $fileKey): string;

    /**
     * Сохраняет файл из fileSource в хранилище, возвращает ключ файла в хранилище
     * @param string $fileSource
     * @return string
     */
    public function save(string $fileSource): string;

    /**
     * Сохраняет контент в файле (существующем или новом), возвращает ключ файла в хранилище
     *
     * @param string $content
     * @param string|null $fileKey
     *
     * @return string
     */
    public function saveContent(string $content, string $fileKey = null): string;

    /**
     * @param string $fileKey
     * @return string
     */
    public function getContent(string $fileKey): string;
}
