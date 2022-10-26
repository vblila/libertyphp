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
        } catch (Exception $exception) {
            $requestId = (string) mt_rand(1000, 9999);
        }

        $this->stream    = $stream;
        $this->requestId = $requestId;
    }

    public function log($level, $message, array $context = [])
    {
        $date = DateTime::createFromFormat('U.u', microtime(true));
        $loggedString = $date->format('Y-m-d H:i:s.u') . ' ' . $this->requestId . ' ' . $level . ': ' . $message;

        if ($context) {
            $loggedString .= PHP_EOL . var_export($context, true);
        }

        $this->stream->write($loggedString . PHP_EOL);
    }
}
