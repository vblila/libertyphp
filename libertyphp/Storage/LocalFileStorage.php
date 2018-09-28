<?php

namespace Libertyphp\Storage;

use Exception;

class LocalFileStorage implements FileStorageInterface
{
    /** @var string */
    protected $storagePath;

    /** @var string|null */
    protected $publicPath;

    /**
     * @param string $storagePath
     * @param string $publicPath
     */
    public function __construct($storagePath, $publicPath = null)
    {
        $this->storagePath = $storagePath;
        $this->publicPath = $publicPath;
    }

    private function getServerSource($fileKey)
    {
        return $this->storagePath . '/' . $this->getSubDirSource($fileKey);
    }

    private function getSubDirSource($fileKey)
    {
        $dir1 = substr($fileKey, 0, 2);
        $dir2 = substr($fileKey, 2, 2);

        return $dir1 . '/' . $dir2 . '/' . $fileKey;
    }

    public function getFileUrl($fileKey)
    {
        if (!$this->publicPath) {
            throw new Exception('Public path is empty');
        }

        return $this->publicPath . '/' . $this->getSubDirSource($fileKey);
    }

    public function save($fileSource)
    {
        if (!file_exists($fileSource)) {
            throw new Exception("File {$fileSource} not found");
        }

        $fileKey = $this->uuid();
        $serverSource = $this->getServerSource($fileKey);
        $serverPath = dirname($serverSource);

        if (!file_exists($serverPath)) {
            mkdir($serverPath, 0777, true);
        }

        rename($fileSource, $serverSource);

        return $fileKey;
    }

    public function saveContent($content, $fileKey = null)
    {
        if ($fileKey && !file_exists($this->getServerSource($fileKey))) {
            throw new Exception("File {$fileKey} not found");
        }

        $fileKey = $fileKey ?? $this->uuid();

        $serverSource = $this->getServerSource($fileKey);
        $serverPath = dirname($serverSource);

        if (!file_exists($serverPath)) {
            mkdir($serverPath, 0777, true);
        }

        $fileResource = fopen($serverSource, 'w');

        fwrite($fileResource, $content);
        fclose($fileResource);

        return $fileKey;
    }

    public function getContent($fileKey)
    {
        $serverSource = $this->getServerSource($fileKey);
        if (!file_exists($serverSource)) {
            throw new Exception("File {$fileKey} not found");
        }

        return file_get_contents($serverSource);
    }

    private function uuid()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0fff ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }
}
