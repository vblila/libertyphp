<?php

namespace Libertyphp\Mailer;

interface MailerInterface
{
    public function send(Email $email): bool;
}
