<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Maintenance;
use App\Service\CompanyService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Maintenance>
 */
class MaintenanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private CompanyService $companyService)
    {
        parent::__construct($registry, Maintenance::class);
    }


    public function findMaintenanceByCompany(?Company $company): array
    {
        $this->companyService->isCompanyExist($company);

        return $this->createQueryBuilder('m')
            ->leftJoin('m.item', 'i')
            ->where('i.company = :companyId')
            ->orderBy('i.company', 'ASC')
            ->setParameter('companyId', $company->getId())
            ->getQuery()
            ->getResult();
    }
}
