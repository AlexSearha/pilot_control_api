<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\Item;
use App\Entity\Maintenance;
use App\Repository\MaintenanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MaintenanceService
{
     private $errorsToStrigify = [];

    public function __construct(
        private CompanyService $companyService,
        private ItemService $itemService,
        private ValidatorInterface $validator,
        private EntityManagerInterface $em,
        private MaintenanceRepository $maintenanceRepo
    )
    {}

    public function getAllMaintenances() : array
    {
        return $this->maintenanceRepo->findBy([], ['startedAt' => "ASC"]);
    }

    public function getAllClientMaintenances(?Company $company, ?Item $item): array
    {
        $this->itemService->isItemExist($item);
        $this->companyService->isCompanyExist($company);

        return $this->maintenanceRepo->findMaintenanceByCompany($company);
    }

    public function getClientMaintenance(?Company $company, ?Item $item, ?Maintenance $maintenance) : Maintenance
    {
        $this->companyService->isCompanyExist($company);
        $this->itemService->isItemExist($item);
        $this->isMaintenanceExiste($maintenance);

        return $maintenance;
    }

    // public function createClientMaintenance(?Company $company, array $payload) : Maintenance
    // {
    //     $this->companyService->isCompanyExist($company);

    //     if (count($payload) === 0) {
    //          throw new \Exception("Aucune donnée à traiter", Response::HTTP_BAD_REQUEST);
    //     }

    //     if ($payload['']) {
    //         # code...
    //     }
    // }

    public function isMaintenanceExiste(?Maintenance $maintenance) : void
    {
        if (!$maintenance) {
            throw new \Exception("Fournisseur inconnu", Response::HTTP_FOUND);
        }
    }

}
