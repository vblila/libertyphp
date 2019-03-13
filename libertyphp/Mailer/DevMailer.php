<?php

namespace Libertyphp\Mailer;

use Psr\Log\LoggerInterface;

class DevMailer implements MailerInterface
{
    /** @var LoggerInterface */
    private $logger;

    /** @var string */
    private $host;

    /** @var string */
    private $port;

    /** @var string */
    private $login;

    /** @var string */
    private $password;

    /** @var string (ssl, tsl) */
    private $secure;

    /** @var string */
    private $defaultFromName;

    /** @var string */
    private $defaultFromEmail;

    public function __construct(
        string $host,
        string $port,
        string $login,
        string $password,
        string $secure,
        string $defaultFromName,
        string $defaultFromEmail,
        LoggerInterface $logger = null
    ) {
        $this->logger = $logger;

        $this->host = $host;
        $this->port = $port;
        $this->login = $login;
        $this->password = $password;
        $this->secure = $secure;

        $this->defaultFromName = $defaultFromName;
        $this->defaultFromEmail = $defaultFromEmail;
    }

    public function send(Email $email): bool
    {
        $this->logger->info(
            "DevMailer sending to: {$email->toEmail}, subject: {$email->subject}"
                . PHP_EOL . $email->body
        );

        return true;
    }
}
