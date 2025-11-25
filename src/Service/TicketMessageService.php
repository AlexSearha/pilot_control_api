<?php

namespace App\Service;

use App\Entity\Ticket;
use App\Entity\TicketMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TicketMessageService extends AbstractController
{
    private $errorToStringify = [];

    public function __construct(
        private TicketService $ticketService,
        private UserService $userService,
        private ValidatorInterface $validator,
        private EntityManagerInterface $em
    ) {
    }

    public function createTicketMessage(?Ticket $ticket, array $payload): Ticket
    {
        if (count($payload) === 0) {
            throw new \Exception("Aucune donnée à traiter", Response::HTTP_BAD_REQUEST);
        }

        $this->ticketService->isTicketExist($ticket);

        $newTicketMessage = new TicketMessage();
        $user =  $this->userService->getUser();

        $newTicketMessage
            ->setSender($user)
            ->setTicket($ticket);

        if (isset($payload['message'])) {
            $newTicketMessage->setMessage($payload['message']);
        }

        $errors = $this->validator->validate($newTicketMessage);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->errorToStringify[] = $error->getMessage();
            }

            throw new \Exception(implode(",", $this->errorToStringify), Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->em->persist($newTicketMessage);
            $this->em->flush();
            $this->em->refresh($ticket);

            return $ticket;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }

    }

}
