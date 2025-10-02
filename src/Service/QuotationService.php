<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\Quotation;
use App\Repository\QuotationRepository;
use Symfony\Component\HttpFoundation\Response;

class QuotationService
{
    public function __construct(
        private QuotationRepository $quotationRepo,
        private CompanyService $companyService
    ) {}

    public function getAllQuotations()
    {
        return $this->quotationRepo->findBy([], ['title' => 'ASC']);
    }

    public function getClientQuotations(?Company $company) : array
    {
        return $this->quotationRepo->findBy(['company' => $company->getId()], ['createAt' => 'ASC']);
    }

    public function getOneClientQuotation(?Company $company, ?Quotation $quotation) : object
    {
        $this->companyService->isCompanyExist($company);
        $this->isQuotationExist($quotation);

        return $this->quotationRepo->findOneBy(['uuid' => $quotation->getUuid()]);
    }

    public function isQuotationExist(?Quotation $quotation) : Quotation
    {
        if (!$quotation) {
            throw new \Exception("Article inconnu", Response::HTTP_NOT_FOUND);
        }

        return $quotation;
    }
}
