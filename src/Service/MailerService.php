<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    public function __construct(
        private MailerInterface $mailer
    )
    {}

    public function sendSimpleEmail(string $token)
    {

        $email = (new Email())
            ->from('alex@controlpilot.fr')
            ->to('alexma225@hotmail.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('subject')
            ->html('<p>See Twig integration for better HTML integration! le token :</p>' . $token);

            $this->mailer->send($email);
    }

}
