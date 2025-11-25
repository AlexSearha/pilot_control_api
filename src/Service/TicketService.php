<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\Ticket;
use App\Enum\TicketCategoryEnum;
use App\Enum\TicketPriorityEnum;
use App\Enum\TicketStatusEnum;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TicketService extends AbstractController
{
    private $errorToStringify = [];


    public function __construct(
        private TicketRepository $ticketRepo,
        private CompanyService $companyService,
        private UserService $userService,
        private SupplierServices $supplierServices,
        private ValidatorInterface $validator,
        private EntityManagerInterface $em
    ) {
    }

    public function getAllTickets(): array
    {
        return $this->ticketRepo->findBy([], ['name' => 'ASC']);
    }

    public function getAllClientTickets(): array
    {
        $user = $this->userService->getUser();

        return $this->ticketRepo->findBy(['user' => $user->getId()], ['name' => 'ASC']);
    }

    public function createTicket(array $payload): Ticket
    {
        if (count($payload) === 0) {
            throw new \Exception("Aucune donnée à traiter", Response::HTTP_BAD_REQUEST);
        }

        $newTicket = new Ticket();

        if (isset($payload['title'])) {
            $newTicket->setTitle($payload['title']);
        }
        if (isset($payload['description'])) {
            $newTicket->setDescription($payload['description']);
        }
        if (isset($payload['status'])) {
            $newTicket->setStatus(TicketStatusEnum::from($payload['status']));
        }
        if (isset($payload['priority'])) {
            $newTicket->setPriority(TicketPriorityEnum::from($payload['priority']));
        }

        $errors = $this->validator->validate($newTicket);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->errorToStringify[] = $error->getMessage();
            }

            throw new \Exception(implode(",", $this->errorToStringify), Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->em->persist($newTicket);
            $this->em->flush();
            $this->em->refresh($newTicket);

            return $newTicket;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }

    }

    public function updatedMessage(?Ticket $ticket ,array $payload) : Ticket
    {
        $this->isTicketExist($ticket);

        if (count($payload) === 0) {
            throw new \Exception("Aucune donnée à traiter", Response::HTTP_BAD_REQUEST);
        }

        if (isset($payload['title'])) {
            $ticket->setTitle($payload['title']);
        }
        if (isset($payload['description'])) {
            $ticket->setDescription($payload['description']);
        }
        if (isset($payload['status'])) {
            $ticket->setStatus(TicketStatusEnum::from($payload['status']));
        }
        if (isset($payload['priority'])) {
            $ticket->setPriority(TicketPriorityEnum::from($payload['priority']));
        }
        if (isset($payload['category'])) {
            $ticket->setCategory(TicketCategoryEnum::from($payload['category']));
        }

        $errors = $this->validator->validate($ticket);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->errorToStringify[] = $error->getMessage();
            }

            throw new \Exception(implode(",", $this->errorToStringify), Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->em->flush();
            $this->em->refresh($ticket);

            return $ticket;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    public function archiveTicket(?Ticket $ticket)
    {
        $this->isTicketExist($ticket);
        $ticket->setDeletedAt(new \DateTimeImmutable());

        try {
            $this->em->flush();
            return $ticket;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }


    public function isTicketExist(?Ticket $ticket)
    {
        if (!$ticket) {
            throw new \Exception("Ticket inconnu", Response::HTTP_NOT_FOUND);
        }

        return $ticket;
    }

}
