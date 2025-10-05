<?php

namespace App\Service;

use App\Entity\Item;
use App\Entity\Quotation;
use App\Entity\QuotationItem;
use App\Repository\QuotationItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class QuotationItemService extends AbstractController
{
    private $errorToStringify = [];

    public function __construct(
        private QuotationItemRepository $quotationItemRepo,
        private ItemService $itemService,
        private EntityManagerInterface $em,
        private ValidatorInterface $validator,
    )
    {}

    public function getAllQuotatioItems()
    {
        return $this->quotationItemRepo->findBy([], ['createdAt' => 'ASC']);
    }

    public function getClientAllQuotationItems(?Item $item)
    {
        $this->itemService->isItemExist($item);

        return $this->quotationItemRepo->findBy(['item' => $item->getId(), ["createdAt" => "ASC"] ]);
    }

    public function getClientQuotationItem(?Item $item, ?QuotationItem $quotationItem) : QuotationItem
    {
        $this->itemService->isItemExist($item);
        $this->isQuotationItemExist($quotationItem);

        return $quotationItem;
    }

    public function findClientQuotationItemByUuid(string $quotationItemUuid) : QuotationItem
    {
        $quotationItem = $this->quotationItemRepo->findOneBy(['uuid' => $quotationItemUuid]);
        $this->isQuotationItemExist($quotationItem);

        return $quotationItem;
    }

    public function createQuotationItem(?Quotation $quotation, array $payload) : QuotationItem
    {
        if (count($payload) === 0) {
            throw new \Exception("Aucune donnée à traiter", Response::HTTP_NOT_FOUND);
        }

        if (!isset($payload['itemUuid'])) {
            throw new \Exception("L'ID de l'article est manquant", Response::HTTP_BAD_REQUEST);
        }


        $newQuotationItem = new QuotationItem();

        $item = $this->itemService->findClientItem($payload['itemUuid']);

        $newQuotationItem->setItem($item);
        $newQuotationItem->setQuotation($quotation);

        if (isset($payload['description'])) {
            $newQuotationItem->setDescription($payload['description']);
        }
        if (isset($payload['quantity'])) {
            $newQuotationItem->setQuantity($payload['quantity']);
        }
        if (isset($payload['unit'])) {
            $newQuotationItem->setUnit($payload['unit']);
        }
        if (isset($payload['totalPrice'])) {
            $newQuotationItem->setTotalPrice($payload['totalPrice']);
        }

        $errors = $this->validator->validate($newQuotationItem);

        if (count($errors) > 0) {

            foreach ($errors as $error) {
                $this->errorToStringify[] = $error->getMessage();
            }

            throw new \Exception(implode(',', $this->errorToStringify), Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->em->persist($newQuotationItem);
            $this->em->flush();
            $this->em->refresh($newQuotationItem);

            return $newQuotationItem;

        } catch (\Exception $e) {

            throw new \Exception('Une erreur est survenue', Response::HTTP_BAD_REQUEST);
        }

    }

    public function updateQuotationItem(?QuotationItem $quotationItem, array $payload) : QuotationItem
    {
        if (count($payload) === 0) {
            throw new \Exception("Aucune donnée à traiter", Response::HTTP_NOT_FOUND);
        }

        if (isset($payload['description'])) {
            $quotationItem->setDescription($payload['description']);
        }
        if (isset($payload['quantity'])) {
            $quotationItem->setQuantity($payload['quantity']);
        }
        if (isset($payload['unit'])) {
            $quotationItem->setUnit($payload['unit']);
        }
        // TODO: Voir si le calcul je le fais en front et/ou en back
        if (isset($payload['totalPrice'])) {
            $quotationItem->setTotalPrice($payload['totalPrice']);
        }

        $errors = $this->validator->validate($quotationItem);

        if (count($errors) > 0) {

            foreach ($errors as $error) {
                $this->errorToStringify[] = $error->getMessage();
            }

            throw new \Exception(implode(',', $this->errorToStringify), Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->em->flush();
            $this->em->refresh($quotationItem);

            return $quotationItem;

        } catch (\Exception $e) {

            throw new \Exception('Une erreur est survenue', Response::HTTP_BAD_REQUEST);
        }
    }

    public function deleteQuotationItem(?QuotationItem $quotationItem) : void
    {
        $this->isQuotationItemExist($quotationItem);

         try {
            $this->em->remove($quotationItem);
            $this->em->flush();

        } catch (\Exception $e) {

            throw new \Exception('Une erreur est survenue', Response::HTTP_BAD_REQUEST);
        }

    }

    public function deleteQuotationItemByUuid(string $quotationItemUuid) : void
    {
        $quotationItem = $this->findClientQuotationItemByUuid($quotationItemUuid);
        $this->deleteQuotationItem($quotationItem);
    }

    public function deleteQuotationItems(array $payload) : void
    {
        if (count($payload) === 0) {
            throw new \Exception("Aucune donnée à traiter", Response::HTTP_BAD_REQUEST);
        }

        foreach ($payload['quotationItems'] as $quotationItemUuid) {

            $quotationItem = $this->quotationItemRepo->findOneBy(['uuid' => $quotationItemUuid]);

            $this->deleteQuotationItem($quotationItem);
        }
    }

    public function isQuotationItemExist(?QuotationItem $quotationItem) : QuotationItem
    {
        if (!$quotationItem) {
            throw new \Exception("l'article du devis est inconnu", Response::HTTP_NOT_FOUND);

        }

        return $quotationItem;
    }
}
