<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\Item;
use App\Repository\ItemRepository;
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
        private EntityManagerInterface $em
    )
    {}

    public function getAllItems()
    {
        return $this->itemRepo->findBy([],['name' => "ASC"]);
    }

    public function getClientAllItems(Company|null $company)
    {
        $this->companyService->isCompanyExist($company);

        return $this->itemRepo->findBy(['company' => $company->getId()], ['name' => 'ASC']);
    }

    public function getClientItem(Company|null $company, Item|null $item)
    {
        $this->companyService->isCompanyExist($company);
        $this->isItemExist($item);

    }

    public function createClientItem(Company|null $company, array $payload)
    {
       $this->companyService->isCompanyExist($company);

       $newItem = new Item();

       $newItem->setCompany($company);

       // TODO: Ajouter le supplier des qu'il sera mis en place

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

    public function isItemExist(Item|null $item): Item
    {
        if (!$item) {
            throw new \Exception("Article inconnu", Response::HTTP_NOT_FOUND);
        }

        return $item;
    }

}
