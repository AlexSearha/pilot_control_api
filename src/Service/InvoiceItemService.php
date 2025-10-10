<?php

namespace App\Service;

use App\Entity\Invoice;
use App\Entity\InvoiceItem;
use App\Entity\Item;
use App\Repository\InvoiceItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class InvoiceItemService extends AbstractController
{
    private $errorToStringify = [];

    public function __construct(
        private InvoiceItemRepository $invoiceItemRepo,
        private ItemService $itemService,
        private ValidatorInterface $validator,
        private EntityManagerInterface $em,
    ) {}

    public function getAllInvoiceItems() : array
    {
        return $this->invoiceItemRepo->findBy([], ['createdAt' => 'ASC']);

    }

    public function getAllClientInvoiceItems(?Item $item) : array
    {
        $this->itemService->isItemExist($item);

        return $this->invoiceItemRepo->findBy(['item' => $item->getId(), ["createdAt" => "ASC"] ]);
    }

    public function findClientInvoiceItemByUuid(string $invoiceItemUuid) : InvoiceItem
    {
        $invoiceItem = $this->invoiceItemRepo->findOneBy(['uuid' => $invoiceItemUuid]);
        $this->isInvoiceItemExist($invoiceItem);

        return $invoiceItem;
    }

    public function createInvoiceItem(?Invoice $invoice, array $payload) : InvoiceItem
    {
        if (count($payload) === 0) {
            throw new \Exception("Aucune donnée à traiter", Response::HTTP_NOT_FOUND);
        }

        if (!isset($payload['itemUuid'])) {
            throw new \Exception("L'ID de l'article est manquant", Response::HTTP_BAD_REQUEST);
        }


        $newInvoiceItem = new InvoiceItem();

        $item = $this->itemService->findClientItem($payload['itemUuid']);

        $newInvoiceItem->setItem($item);
        $newInvoiceItem->setInvoice($invoice);

        if (isset($payload['description'])) {
            $newInvoiceItem->setDescription($payload['description']);
        }
        if (isset($payload['quantity'])) {
            $newInvoiceItem->setQuantity($payload['quantity']);
        }
        if (isset($payload['unit'])) {
            $newInvoiceItem->setUnit($payload['unit']);
        }
        if (isset($payload['totalPrice'])) {
            $newInvoiceItem->setTotalPrice($payload['totalPrice']);
        }

        $errors = $this->validator->validate($newInvoiceItem);

        if (count($errors) > 0) {

            foreach ($errors as $error) {
                $this->errorToStringify[] = $error->getMessage();
            }

            throw new \Exception(implode(',', $this->errorToStringify), Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->em->persist($newInvoiceItem);
            $this->em->flush();
            $this->em->refresh($newInvoiceItem);

            return $newInvoiceItem;

        } catch (\Exception $e) {

            throw new \Exception('Une erreur est survenue', Response::HTTP_BAD_REQUEST);
        }

    }

    public function updateQuotationItem(?InvoiceItem $invoiceItem, array $payload) : InvoiceItem
    {
        if (count($payload) === 0) {
            throw new \Exception("Aucune donnée à traiter", Response::HTTP_NOT_FOUND);
        }

        if (isset($payload['description'])) {
            $invoiceItem->setDescription($payload['description']);
        }
        if (isset($payload['quantity'])) {
            $invoiceItem->setQuantity($payload['quantity']);
        }
        if (isset($payload['unit'])) {
            $invoiceItem->setUnit($payload['unit']);
        }
        // TODO: Voir si le calcul je le fais en front et/ou en back
        if (isset($payload['totalPrice'])) {
            $invoiceItem->setTotalPrice($payload['totalPrice']);
        }

        $errors = $this->validator->validate($invoiceItem);

        if (count($errors) > 0) {

            foreach ($errors as $error) {
                $this->errorToStringify[] = $error->getMessage();
            }

            throw new \Exception(implode(',', $this->errorToStringify), Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->em->flush();
            $this->em->refresh($invoiceItem);

            return $invoiceItem;

        } catch (\Exception $e) {

            throw new \Exception('Une erreur est survenue', Response::HTTP_BAD_REQUEST);
        }
    }

    public function deleteInvoiceItem(?InvoiceItem $invoiceItem) : void
    {
        $this->isInvoiceItemExist($invoiceItem);

         try {
            $this->em->remove($invoiceItem);
            $this->em->flush();

        } catch (\Exception $e) {

            throw new \Exception('Une erreur est survenue', Response::HTTP_BAD_REQUEST);
        }

    }

    public function deleteInvoiceItemByUuid(string $invoiceItemUuid) : void
    {
        $invoiceitem = $this->findClientInvoiceItemByUuid($invoiceItemUuid);
        $this->deleteInvoiceItem($invoiceitem);
    }

    public function deleteInvoiceItems(array $payload) : void
    {
        if (count($payload) === 0) {
            throw new \Exception("Aucune donnée à traiter", Response::HTTP_BAD_REQUEST);
        }

        foreach ($payload['InvoiceItems'] as $invoiceItemUuid) {

            $this->deleteInvoiceItemByUuid($invoiceItemUuid);
        }
    }

    public function isInvoiceItemExist(?InvoiceItem $invoiceItem) : InvoiceItem
    {
        if (!$invoiceItem) {
            throw new \Exception("l'article de la facture est inconnu", Response::HTTP_NOT_FOUND);

        }

        return $invoiceItem;
    }
}
