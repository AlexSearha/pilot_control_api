<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\Invoice;
use App\Enum\InvoiceStatusEnum;
use App\Repository\InvoiceRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class InvoiceService extends AbstractController
{
    private $errorsToStrigify = [];

    public function __construct(
        private InvoiceRepository $invoiceRepo,
        private ValidatorInterface $validator,
        private EntityManagerInterface $em,
        private InvoiceItemService $invoiceItemService,
        private CompanyService $companyService,
        private QuotationService $quotationService,
        private CompanyClientService $companyClientService,
        private ProjectService $projectService
    ) {
    }

    public function getAllInvoices()
    {
        return $this->invoiceRepo->findBy([], ['invoiceNumber' => 'ASC']);
    }

    public function getAllClientInvoices(?Company $company): array
    {
        $this->companyService->isCompanyExist($company);

        return $this->invoiceRepo->findBy(['company' => $company->getId(), ['createdAt' => 'ASC']]);
    }

    public function getOneClientInvoice(?Company $company, ?Invoice $invoice): Invoice
    {
        $this->companyService->isCompanyExist($company);
        $this->isInvoiceExist($invoice);

        return $invoice;
    }

    public function findClientInvoice(string $invoiceUuid): Invoice
    {
        $invoice = $this->invoiceRepo->findOneBy(['uuid' => $invoiceUuid]);

        $this->isInvoiceExist($invoice);

        return $invoice;
    }

    public function createClientInvoice(?Company $company, array $payload): Invoice
    {
        $this->companyService->isCompanyExist($company);

        if (count($payload) === 0) {
            throw new \Exception('Aucune donnée à traiter', Response::HTTP_BAD_REQUEST);
        }

        if (!isset($payload['invoiceItems'])) {
            throw new \Exception('Aucun article de renseigné', Response::HTTP_BAD_REQUEST);
        }

        $newInvoice = new Invoice();
        $newInvoice->setCompany($company);

        if (isset($payload['invoiceNumber'])) {
            $newInvoice->setInvoiceNumber($payload['invoiceNumber']);
        }
        if (isset($payload['issueDate'])) {
            $newInvoice->setIssueDate(new DateTimeImmutable($payload['issueDate']));
        }
        if (isset($payload['dueDate'])) {
            $newInvoice->setDueDate(new DateTimeImmutable($payload['dueDate']));
        }
        if (isset($payload['status'])) {
            $newInvoice->setStatus($payload['status']);
        }
        if (isset($payload['amountHt'])) {
            $newInvoice->setAmountHt($payload['amountHt']);
        }
        if (isset($payload['amountTtc'])) {
            $newInvoice->setAmountTtc($payload['amountTtc']);
        }
        if (isset($payload['taxRate'])) {
            $newInvoice->setTaxRate($payload['taxRate']);
        }
        if (isset($payload['comments'])) {
            $newInvoice->setComments($payload['comments']);
        }

        foreach ($payload['invoiceItems'] as $invoiceItemPayload) {
            /** @var array $invoiceItemPayload */
            $newQuotationItem = $this->invoiceItemService->createInvoiceItem($newInvoice, $invoiceItemPayload);
            $newInvoice->addInvoiceItem($newQuotationItem);
        }

        // TODO: Intégrer le payment une fois qu'il sera ajouté
        // if (isset($payload['payments'])) {
        //      foreach ($payload['payments'] as $invoiceItemPayload) {
        //         /** @var array $invoicePaymentPayload */
        //         $newInvoicePayment = $this->invoiceItemService->createInvoiceItem($newInvoice, $invoicePaymentPayload);
        //         $newInvoice->addPayment($newInvoicePayment);
        //     }
        // }

        if (isset($payload['quotation'])) {
            $newInvoice->setQuotation($payload['quotation']);
        }
        if (isset($payload['companyClient'])) {
            $newInvoice->setCompanyClient($payload['companyClient']);
        }
        if (isset($payload['project'])) {
            $newInvoice->setProject($payload['project']);
        }

        $errors = $this->validator->validate($newInvoice);

        if (count($errors) > 0) {

            foreach ($errors as $error) {
                $this->errorsToStrigify[] = $error->getMessage();
            }

            throw new \Exception(implode(',', $this->errorsToStrigify), Response::HTTP_BAD_REQUEST);
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

    public function updateClientInvoice(?Company $company, ?Invoice $invoice, array $payload): Invoice
    {

        $this->companyService->isCompanyExist($company);

        switch ($invoice->getStatus()) {
            case InvoiceStatusEnum::PAID:
            case InvoiceStatusEnum::PARTIALLY_PAID:
            case InvoiceStatusEnum::CANCELLED:
                throw new \Exception("Action interdite sur une facture déjà payée ou annulée", Response::HTTP_UNAUTHORIZED);

            case InvoiceStatusEnum::DRAFT:
                $authorisation = 'full';
                break;

            case InvoiceStatusEnum::SENT:
                $authorisation = 'restricted';
                break;

            case InvoiceStatusEnum::OVERDUE:
                $authorisation = 'limited';
                break;

            default:
                throw new \Exception("Statut de facture inconnu", Response::HTTP_BAD_REQUEST);
        }

        if (count($payload) === 0) {
            throw new \Exception("Aucune donnée à traiter", Response::HTTP_BAD_REQUEST);
        }


        $modifiableFields = [];

        switch ($authorisation) {
            case 'full':
                $modifiableFields = [
                    'invoiceNumber', 'issueDate', 'dueDate', 'status',
                    'amountHt', 'amountTtc', 'taxRate', 'comments',
                    'invoiceItems', 'quotation', 'companyClient', 'project', 'payment'
                ];
                break;

            case 'restricted':
                $modifiableFields = [
                    'dueDate', 'comments'
                ];
                break;

            case 'limited':
                $modifiableFields = [
                    'comments', "payment"
                ];
                break;
        }


        if (in_array('invoiceNumber', $modifiableFields) && isset($payload['invoiceNumber'])) {
            $invoice->setInvoiceNumber($payload['invoiceNumber']);
        }

        if (in_array('issueDate', $modifiableFields) && isset($payload['issueDate'])) {
            $invoice->setIssueDate(new DateTimeImmutable($payload['issueDate']));
        }

        if (in_array('dueDate', $modifiableFields) && isset($payload['dueDate'])) {
            $invoice->setDueDate(new DateTimeImmutable($payload['dueDate']));
        }

        if (in_array('status', $modifiableFields) && isset($payload['status'])) {
            $invoice->setStatus($payload['status']);
        }

        if (in_array('amountHt', $modifiableFields) && isset($payload['amountHt'])) {
            $invoice->setAmountHt($payload['amountHt']);
        }

        if (in_array('amountTtc', $modifiableFields) && isset($payload['amountTtc'])) {
            $invoice->setAmountTtc($payload['amountTtc']);
        }

        if (in_array('taxRate', $modifiableFields) && isset($payload['taxRate'])) {
            $invoice->setTaxRate($payload['taxRate']);
        }

        if (in_array('comments', $modifiableFields) && isset($payload['comments'])) {
            $invoice->setComments($payload['comments']);
        }

        if (in_array('invoiceItems', $modifiableFields) && isset($payload['invoiceItemsAdd'])) {
            foreach ($payload['invoiceItemsAdd'] as $invoiceItemPayload) {
                $newInvoiceItem = $this->invoiceItemService->createInvoiceItem($invoice, $invoiceItemPayload);
                $invoice->addInvoiceItem($newInvoiceItem);
            }
        }

        if (in_array('invoiceItems', $modifiableFields) && isset($payload['invoiceItemsRemove'])) {
            foreach ($payload['invoiceItemsRemove'] as $invoiceItemUuid) {
                $newInvoiceItem = $this->invoiceItemService->findClientInvoiceItemByUuid($invoiceItemUuid);
                $invoice->removeInvoiceItem($newInvoiceItem);
                $this->invoiceItemService->deleteInvoiceItemByUuid($invoiceItemUuid);
            }
        }

        // TODO: Ajouter ces methodes des ue Payment sera ajouté

        // if (in_array('payment', $modifiableFields) && isset($payload['paymentAdd'])) {
        //     foreach ($payload['paymentAdd'] as $paymentUuid) {
        //         $newPayment = $this->invoiceItemService->createInvoiceItem($invoice, $invoiceItemPayload);
        //         $invoice->addPayment($newPayment);
        //     }
        // }

        // if (in_array('payment', $modifiableFields) && isset($payload['paymentRemove'])) {
        //     foreach ($payload['paymentRemove'] as $paymentUuid) {
        //         $payment = $this->payment->findClientInvoiceItemByUuid($paymentUuid);
        //         $invoice->removePayment($payment);
        //         $this->invoiceItemService->deleteInvoiceItemByUuid($invoiceItemUuid);
        //     }
        // }

        if (in_array('quotation', $modifiableFields) && isset($payload['quotation'])) {
            $quotation = $this->quotationService->findQuotationByUuid($payload['quotation']);
            $invoice->setQuotation($quotation);
        }

        if (in_array('companyClient', $modifiableFields) && isset($payload['companyClient'])) {
            $companyClient = $this->companyClientService->findCompanyClientByUuid($payload['companyClient']);
            $invoice->setCompanyClient($companyClient);
        }

        if (in_array('project', $modifiableFields) && isset($payload['project'])) {
            $project = $this->projectService->findProjectByUuid($payload['project']);
            $invoice->setProject($project);
        }

        try {
            $this->em->flush();
            $this->em->refresh($invoice);

            return $invoice;
        } catch (\Exception $e) {
            throw new \Exception("Une erreur est survenue", Response::HTTP_BAD_REQUEST);
        }
    }

    public function deleteClientInvoice(?Company $company, ?Invoice $invoice): void
    {
        $this->companyService->isCompanyExist($company);
        $this->isInvoiceExist($invoice);

        try {
            $this->em->flush();

        } catch (\Exception $e) {
            throw new \Exception("Une erreur est survenue", Response::HTTP_BAD_REQUEST);
        }
    }

    public function deleteClientInvoices(?Company $company, array $payload): void
    {

        if (count($payload['invoices']) === 0) {
            throw new \Exception("Aucune donnée à traiter", Response::HTTP_BAD_REQUEST);
        }

        $this->companyService->isCompanyExist($company);

        foreach ($payload['invoices'] as $invoiceUuid) {
            $invoice = $this->findClientInvoice($invoiceUuid);
            $this->deleteClientInvoice($company, $invoice);
        }

    }

    public function isInvoiceExist(?Invoice $invoice): void
    {
        if (!$invoice) {
            throw new \Exception("Article inconnu", Response::HTTP_NOT_FOUND);
        }

    }
}
