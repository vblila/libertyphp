<?php

namespace Libertyphp\Mailer;

use Psr\Log\LoggerInterface;
use Tx\Mailer;

class SmtpMailer implements MailerInterface
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
        $fromName  = $email->fromName ?? $this->defaultFromName;
        $fromEmail = $email->fromEmail ?? $this->defaultFromEmail;

        $result = (new Mailer())
            ->setServer($this->host, $this->port, $this->secure)
            ->setAuth($this->login, $this->password)
            ->setFrom($fromName, $fromEmail)
            ->addTo($email->toName, $email->toEmail)
            ->addBcc($fromName, $fromEmail)
            ->setSubject($email->subject)
            ->setBody($email->body)
            ->send();

        $bodyBase64Encoded = base64_encode($email->body);

        $this->logger->info("SmtpMailer sending to: {$email->toEmail}, subject: {$email->subject}");
        $this->logger->info("Body in base64: {$bodyBase64Encoded}");

        if ($result) {
            $this->logger->info('Result: successful');
        } else {
            $this->logger->warning('Result: failed');
        }

        return $result;
    }
}
