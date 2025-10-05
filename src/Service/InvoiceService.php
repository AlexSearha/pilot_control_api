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
        private InvoiceItemService $invoiceItemService,
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

    public function createClientInvoice(?Company $company, array $payload): Invoice
    {
        $this->companyService->isCompanyExist($company);

        if (count($payload) === 0 ) {
            throw new \Exception('Aucune donnée à traiter', Response::HTTP_BAD_REQUEST);
        }

        if (!isset($payload['invoiceItems'])) {
            throw new \Exception('Aucun article de renseigné', Response::HTTP_BAD_REQUEST);
        }

        $newInvoice = new Invoice();
        $newInvoice->setCompany($company);

        foreach ($payload['invoiceItems'] as $invoiceItemPayload) {
            /** @var array $invoiceItemPayload */
            $newQuotationItem = $this->invoiceItemService->createInvoiceItem($newInvoice, $invoiceItemPayload);
            $newInvoice->addInvoiceItem($newQuotationItem);
        }

        if (isset($payload['invoiceNumber'])) {
            $newInvoice->setInvoiceNumber($payload['invoiceNumber']);
        }
        if (isset($payload['invoiceNumber'])) {
            $newInvoice->setInvoiceNumber($payload['invoiceNumber']);
        }

        // TODO: Continuer l'intégration des element de l'entité INVOICE


        $errors = $this->validator->validate($newInvoice);

        if (count($errors) > 0) {

            foreach ($errors as $error) {
                $this->errorToStringify[] = $error->getMessage();
            }

            throw new \Exception(implode(',', $this->errorToStringify), Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->em->persist($newInvoice);
            $this->em->flush();
            $this->em->refresh($newInvoice);

            return $newInvoice;

        } catch (\Exception $e) {

            throw new \Exception('Une erreur est survenue', Response::HTTP_BAD_REQUEST);

        }
    }

    public function isInvoiceExist(?Invoice $invoice) : void
    {
        if (!$invoice) {
            throw new \Exception("Article inconnu", Response::HTTP_NOT_FOUND);
        }

    }
}
