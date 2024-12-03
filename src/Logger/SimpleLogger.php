<?php

namespace Libertyphp\Logger;

use DateTime;
use Exception;
use Libertyphp\Utils\Random;
use Psr\Http\Message\StreamInterface;
use Psr\Log\AbstractLogger;

class SimpleLogger extends AbstractLogger
{
    private StreamInterface $stream;

    private string $requestId;

    public function __construct(StreamInterface $stream, ?string $requestId = null)
    {
        try {
            $requestId = $requestId ?? Random::uuidV4();
        } catch (Exception) {
            $requestId = (string) mt_rand(1000, 9999);
        }

        $this->stream    = $stream;
        $this->requestId = $requestId;
    }

    public function log($level, $message, array $context = [])
    {
        $mtime = microtime(true);
        $date = DateTime::createFromFormat('U.u', $mtime);

        /**
         * @link https://www.php.net/manual/ru/datetime.createfromformat.php#128901
         * Trying to format() will return a fatal error if microtime(true) just so happened to return a float with all zeros as decimals.
         * This is because DateTime::createFromFormat('U.u', $aFloatWithAllZeros) returns false.
         */
        if ($date === false) {
            $date = DateTime::createFromFormat('U', $mtime);
        }

        $loggedString = $date->format('Y-m-d H:i:s.u') . ' ' . $this->requestId . ' ' . $level . ': ' . $message;

        if ($context) {
            $loggedString .= PHP_EOL . var_export($context, true);
        }

        $this->stream->write($loggedString . PHP_EOL);
    }
}
