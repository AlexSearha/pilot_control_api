<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\Item;
use App\Repository\ItemRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ItemService
{
    private $errorsToStrigify = [];

    public function __construct(
        private ItemRepository $itemRepo,
        private CompanyService $companyService,
        private ValidatorInterface $validator,
        private SupplierServices $supplierServices,
        private EntityManagerInterface $em
    ) {
    }

    public function getAllItems()
    {
        return $this->itemRepo->findBy([], ['name' => "ASC"]);
    }

    public function getClientAllItems(?Company $company): array
    {
        $this->companyService->isCompanyExist($company);

        return $this->itemRepo->findBy(['company' => $company->getId()], ['name' => 'ASC']);
    }

    public function getClientItem(?Company $company, ?Item $item): void
    {
        $this->companyService->isCompanyExist($company);
        $this->isItemExist($item);

    }

    public function createClientItem(?Company $company, array $payload)
    {
        if (count($payload) === 0) {
            throw new \Exception('Aucune donnée à traiter', Response::HTTP_BAD_REQUEST);
        }

        $this->companyService->isCompanyExist($company);

        $newItem = new Item();

        $newItem->setCompany($company);

        if (isset($payload['supplier'])) {
            $supplier = $this->supplierServices->findOneSupplier($payload['supplier']);
            $newItem->setSupplier($supplier);
        }
        if (isset($payload['name'])) {
            $newItem->setName($payload['name']);
        }
        if (isset($payload['status'])) {
            $newItem->setStatus($payload['status']);
        }
        if (isset($payload['description'])) {
            $newItem->setDescription($payload['description']);
        }
        if (isset($payload['quantity'])) {
            $newItem->setQuantity($payload['quantity']);
        }
        if (isset($payload['quatityReserved'])) {
            $newItem->setQuatityReserved($payload['quatityReserved']);
        }
        if (isset($payload['quantityAlertThreshold'])) {
            $newItem->setQuantityAlertThreshold($payload['quantityAlertThreshold']);
        }
        if (isset($payload['serialNumber'])) {
            $newItem->setSerialNumber($payload['serialNumber']);
        }
        if (isset($payload['unit'])) {
            $newItem->setUnit($payload['unit']);
        }
        if (isset($payload['price'])) {
            $newItem->setPrice($payload['price']);
        }

        //  TODO: setter le currency quand il sera mis en place


        $errors = $this->validator->validate($newItem);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->errorsToStrigify[] = $error->getMessage();
            }

            throw new \Exception(implode(",", $this->errorsToStrigify), Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->em->persist($newItem);
            $this->em->flush();
            $this->em->refresh($newItem);

            return $newItem;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    public function updateClientItem(?Company $company, ?Item $item, array $payload): Item
    {
        if (count($payload) === 0) {
            throw new \Exception('Aucune donnée à traiter', Response::HTTP_BAD_REQUEST);
        }

        $this->companyService->isCompanyExist($company);
        $this->isItemExist($item);

        if (isset($payload['supplier'])) {
            $supplier = $this->supplierServices->findOneSupplier($payload['supplier']);
            $item->setSupplier($supplier);
        }
        if (isset($payload['name'])) {
            $item->setName($payload['name']);
        }
        if (isset($payload['status'])) {
            $item->setStatus($payload['status']);
        }
        if (isset($payload['description'])) {
            $item->setDescription($payload['description']);
        }
        if (isset($payload['quantity'])) {
            $item->setQuantity($payload['quantity']);
        }
        if (isset($payload['quatityReserved'])) {
            $item->setQuatityReserved($payload['quatityReserved']);
        }
        if (isset($payload['quantityAlertThreshold'])) {
            $item->setQuantityAlertThreshold($payload['quantityAlertThreshold']);
        }
        if (isset($payload['serialNumber'])) {
            $item->setSerialNumber($payload['serialNumber']);
        }
        if (isset($payload['unit'])) {
            $item->setUnit($payload['unit']);
        }
        if (isset($payload['price'])) {
            $item->setPrice($payload['price']);
        }


        $errors = $this->validator->validate($item);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->errorsToStrigify[] = $error->getMessage();
            }

            throw new \Exception(implode(",", $this->errorsToStrigify), Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->em->flush();
            $this->em->refresh($item);
            return $item;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    public function deleteItem(?Company $company, ?Item $item): void
    {
        $this->companyService->isCompanyExist($company);
        $this->isItemExist($item);

        $item->setdeletedAt(new DateTimeImmutable());

        try {
            $this->em->flush();

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    public function deleteItems(?Company $company, array $payload): void
    {
        $this->companyService->isCompanyExist($company);

        if (!isset($payload['items']) || count($payload['items']) === 0) {
            throw new \Exception("Aucune donnée à traiter", Response::HTTP_BAD_REQUEST);
        }

        foreach ($payload['items'] as $item) {
            $findItem = $this->findClientItem($item);
            $this->deleteItem($company, $findItem);
        }

    }

    public function findClientItem(string $itemUuid): Item
    {
        $item = $this->itemRepo->findOneBy(['uuid' => $itemUuid]);
        $this->isItemExist($item);

        return $item;

    }

    public function isItemExist(?Item $item): Item
    {
        if (!$item) {
            throw new \Exception("Article inconnu", Response::HTTP_NOT_FOUND);
        }

        return $item;
    }

}
