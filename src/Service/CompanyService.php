<?php

namespace App\Service;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class CompanyService
{
    public function __construct(
        private EntityManagerInterface $em,
        private CompanyRepository $companyRepo
    )
    {}

    public function getAllCompanies()
    {
        return $this->companyRepo->findAll();
    }

    public function getOneCompany(string $uuid): Company
    {
        if (!$uuid) {
            throw new \Exception("aucun identifiant reçu", Response::HTTP_BAD_REQUEST);
        }

        $company = $this->companyRepo->findOneBy(['uuid' => $uuid]);
        if (!$company) {
            throw new \Exception("Société inconnue", Response::HTTP_NOT_FOUND);
        }

        return $company;
    }
}
