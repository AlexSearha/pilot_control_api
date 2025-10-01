<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\Supplier;
use App\Entity\SupplierOrder;
use App\Repository\SupplierOrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SupplierOrderService
{
    private $errorToStringify = [];


    public function __construct(
        private SupplierOrderRepository $supplierOrderRepo,
        private CompanyService $companyService,
        private SupplierServices $supplierServices,
        private ValidatorInterface $validator,
        private EntityManagerInterface $em
    ) {}

    public function getAllSupplierOrders(): array
    {
        return $this->supplierOrderRepo->findBy([], ['name' => 'ASC']);
    }

    public function getAllClientSupplierOrders(?Company $company, ?Supplier $supplier) : array
    {
        $this->companyService->isCompanyExist($company);
        $this->supplierServices->isSupplierExist($supplier);

        return $this->supplierOrderRepo->findBy(['company' => $company->getId(), 'supplier' => $supplier->getId()], ['name' => 'ASC']);

    }

    public function getClientSupplierOrder(?Company $company, ?Supplier $supplier, ?SupplierOrder $supplierOrder) : object
    {
        $this->companyService->isCompanyExist($company);
        $this->supplierServices->isSupplierExist($supplier);
        $this->isSupplierOrderExist($supplierOrder);

        return $this->supplierOrderRepo->findOneBy([
            'company' => $company->getId(),
            'supplier' => $supplier->getId(),
            'id' => $supplierOrder->getId()
        ],
        ['company' => 'ASC']);
    }

    public function createSupplierOrder(?Company $company, ?Supplier $supplier, array $payload) : SupplierOrder
    {
        $this->companyService->isCompanyExist($company);
        $this->supplierServices->isSupplierExist($supplier);

        if (count($payload) === 0 ) {
            throw new \Exception('Aucune donnée à traiter', Response::HTTP_BAD_REQUEST);
        }

        $newSupplierOrder = new SupplierOrder();

        $newSupplierOrder->setSupplier($supplier);
        $newSupplierOrder->setCompany($company);

        if (isset($payload['expectedDeliveryDate'])) {
            $newSupplierOrder->setExpectedDeliveryDate($payload['expectedDeliveryDate']);
        }

        if (isset($payload['totalAmount'])) {
            $newSupplierOrder->setTotalAmount($payload['totalAmount']);
        }

        if (isset($payload['comments'])) {
            $newSupplierOrder->setComments($payload['comments']);
        }

        $errors = $this->validator->validate($newSupplierOrder);

        if (count($errors) > 0) {

            foreach ($errors as $error) {
                $this->errorToStringify[] = $error->getMessage();
            }

            throw new \Exception(implode(',', $this->errorToStringify), Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->em->persist($newSupplierOrder);
            $this->em->flush();
            $this->em->refresh($newSupplierOrder);

            return $newSupplierOrder;
        } catch (\Exception $e) {
         throw new \Exception('Une erreur est survenue', Response::HTTP_BAD_REQUEST);

        }

    }

    public function isSupplierOrderExist(?SupplierOrder $supplierOrder) : void
    {
        if (!$supplierOrder) {
            throw new \Exception("Commande inconnu", Response::HTTP_FOUND);

        }
    }

}
