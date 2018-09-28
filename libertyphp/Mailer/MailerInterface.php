<?php

namespace Libertyphp\Mailer;

interface MailerInterface
{
    /**
     * @param Email $email
     * @return bool
     */
    public function send(Email $email);
}
