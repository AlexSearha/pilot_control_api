<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService extends AbstractController
{
    public function __construct(
        private MailerInterface $mailer
    )
    {}

    public function sendSimpleEmail(string $token, User $user, string $subject)
    {

        $email = (new Email())
            ->from($this->getParameter('email_from'))
            ->to($user->getEmail())
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->html('<p>Votre token : </p>' . $token);

            $this->mailer->send($email);
    }

}
