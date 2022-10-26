<?php

namespace Libertyphp\Utils;

use Exception;

class Random
{
    /**
     * @return string Cryptographically secure uuid v4 string
     * @throws Exception
     */
    public static function uuidV4(): string
    {
        $data = random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * @return string Cryptographically secure hex string
     * @throws Exception
     */
    public static function hex(int $bytesCount): string
    {
        $data = random_bytes($bytesCount);
        return bin2hex($data);
    }
}
