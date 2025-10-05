<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\Invoice;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class InvoiceService extends AbstractController
{
    public function __construct(
        private InvoiceRepository $invoiceRepo,
        private ValidatorInterface $validator,
        private EntityManagerInterface $em,
        private CompanyService $companyService
    ) {}

    public function getAllInvoices()
    {
        return $this->invoiceRepo->findBy([], ['invoiceNumber' => 'ASC']);
    }

    public function getAllClientInvoices(?Company $company) : array
    {
        $this->companyService->isCompanyExist($company);

        return $this->invoiceRepo->findBy(['company' => $company->getId(), ['createdAt' => 'ASC']]);
    }

    public function getOneClientInvoice(?Company $company, ?Invoice $invoice) : Invoice
    {
        $this->companyService->isCompanyExist($company);
        $this->isInvoiceExist($invoice);

        return $invoice;
    }

    // TODO: A CONTINUER
    // public function createClientInvoice(?Company $company, array $payload) : Invoice
    // {
    //     $this->companyService->isCompanyExist($company);


    // }

    public function isInvoiceExist(?Invoice $invoice) : void
    {
        if (!$invoice) {
            throw new \Exception("Article inconnu", Response::HTTP_NOT_FOUND);
        }

    }
}
