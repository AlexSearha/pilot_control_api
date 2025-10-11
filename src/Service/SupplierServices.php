<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\Supplier;
use App\Repository\SupplierRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SupplierServices
{
    private $errorToStringify = [];

    public function __construct(
        private SupplierRepository $supplierRepo,
        private CompanyService $companyService,
        private ValidatorInterface $validator,
        private EntityManagerInterface $em
    ) {
    }

    public function getAllSuppliers(): array
    {
        return $this->supplierRepo->findBy([], ['name' => 'ASC']);
    }

    public function getClientSuppliers(?Company $company): array
    {
        $this->companyService->isCompanyExist($company);

        return $this->supplierRepo->findBy(['company' => $company->getId() ]);
    }

    public function getOneSupplier(?Company $company, ?Supplier $supplier): Supplier
    {
        $this->companyService->isCompanyExist($company);
        $this->isSupplierExist($supplier);

        return $supplier;
    }

    public function createSupplier(?Company $company, array $payload): Supplier
    {
        $this->companyService->isCompanyExist($company);

        if (count($payload) === 0) {
            throw new \Exception('Aucune donnée à traiter', Response::HTTP_BAD_REQUEST);
        }

        $findSupplier = $this->supplierRepo->findOneBy(['name' => $payload['name']]);
        if ($findSupplier) {
            throw new \Exception("Le fournisseur existe déjà", Response::HTTP_FOUND);
        }

        $newSupplier = new Supplier();
        $newSupplier->setCompany($company);

        if (isset($payload['email'])) {
            $newSupplier->setEmail($payload['email']);
        }
        if (isset($payload['address'])) {
            $newSupplier->setAddress($payload['address']);
        }
        if (isset($payload['zipcode'])) {
            $newSupplier->setZipcode($payload['zipcode']);
        }
        if (isset($payload['city'])) {
            $newSupplier->setCity($payload['city']);
        }
        if (isset($payload['siret'])) {
            $newSupplier->setSiret($payload['addsiretress']);
        }
        if (isset($payload['siren'])) {
            $newSupplier->setSiren($payload['siren']);
        }
        if (isset($payload['region'])) {
            $newSupplier->setRegion($payload['region']);
        }
        if (isset($payload['vatNumber'])) {
            $newSupplier->setVatNumber($payload['vatNumber']);
        }
        if (isset($payload['activityType'])) {
            $newSupplier->setActivityType($payload['activityType']);
        }
        if (isset($payload['website'])) {
            $newSupplier->setWebsite($payload['website']);
        }
        if (isset($payload['comments'])) {
            $newSupplier->setComments($payload['comments']);
        }

        $errors = $this->validator->validate($newSupplier);

        if (count($errors) > 0) {

            foreach ($errors as $error) {
                $this->errorToStringify[] = $error->getMessage();
            }

            throw new \Exception(implode(',', $this->errorToStringify), Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->em->persist($newSupplier);
            $this->em->flush();
            $this->em->refresh($newSupplier);

            return $newSupplier;
        } catch (\Exception $e) {
            throw new \Exception('Une erreur est survenue', Response::HTTP_BAD_REQUEST);

        }

    }

    public function updateOneSupplier(?Company $company, ?Supplier $supplier, array $payload): Supplier
    {
        $this->companyService->isCompanyExist($company);
        $this->isSupplierExist($supplier);

        if (count($payload) === 0) {
            throw new \Exception('Aucune donnée à traiter', Response::HTTP_BAD_REQUEST);
        }

        if (isset($payload['email'])) {
            $supplier->setEmail($payload['email']);
        }
        if (isset($payload['address'])) {
            $supplier->setAddress($payload['address']);
        }
        if (isset($payload['zipcode'])) {
            $supplier->setZipcode($payload['zipcode']);
        }
        if (isset($payload['city'])) {
            $supplier->setCity($payload['city']);
        }
        if (isset($payload['siret'])) {
            $supplier->setSiret($payload['addsiretress']);
        }
        if (isset($payload['siren'])) {
            $supplier->setSiren($payload['siren']);
        }
        if (isset($payload['region'])) {
            $supplier->setRegion($payload['region']);
        }
        if (isset($payload['vatNumber'])) {
            $supplier->setVatNumber($payload['vatNumber']);
        }
        if (isset($payload['activityType'])) {
            $supplier->setActivityType($payload['activityType']);
        }
        if (isset($payload['website'])) {
            $supplier->setWebsite($payload['website']);
        }
        if (isset($payload['comments'])) {
            $supplier->setComments($payload['comments']);
        }

        $errors = $this->validator->validate($supplier);

        if (count($errors) > 0) {

            foreach ($errors as $error) {
                $this->errorToStringify[] = $error->getMessage();
            }

            throw new \Exception(implode(',', $this->errorToStringify), Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->em->flush();
            $this->em->refresh($supplier);
            return $supplier;

        } catch (\Exception $e) {
            throw new \Exception('Une erreur est survenue', Response::HTTP_BAD_REQUEST);

        }
    }

    public function deleteSupplier(?Company $company, ?Supplier $supplier): void
    {
        $this->companyService->isCompanyExist($company);
        $this->isSupplierExist($supplier);

        $supplier->setDeletedAt(new DateTimeImmutable());

        try {
            $this->em->flush();

        } catch (\Exception $e) {
            throw new \Exception('Une erreur est survenue', Response::HTTP_BAD_REQUEST);

        }
    }

    public function findOneSupplier(string $supplierUuid): Supplier
    {
        $supplier = $this->supplierRepo->findOneBy(['uuid' => $supplierUuid]);

        $this->isSupplierExist($supplier);

        return $supplier;
    }

    public function isSupplierExist(?Supplier $supplier): void
    {
        if (!$supplier) {
            throw new \Exception("Fournisseur inconnu", Response::HTTP_FOUND);

        }
    }
}
