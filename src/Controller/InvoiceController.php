<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Invoice;
use App\Security\Voter\CompanyVoter;
use App\Security\Voter\InvoiceVoter;
use App\Service\FormatService;
use App\Service\InvoiceService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

final class InvoiceController extends AbstractController
{
    public function __construct(
        private InvoiceService $invoiceService,
        private SerializerInterface $serializer,
        private FormatService $format
    ) {
    }

    // ---- Super Admin Routes ----

    #[Route('/api/invoices', name: 'get_all_invoices')]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function getAllInvoices(): JsonResponse
    {
        $invoices = $this->invoiceService->getAllInvoices();
        $serialzeData = $this->serializer->serialize($invoices, 'json', ['groups' => 'get:light_quotation']);
        return $this->format->sendSuccessSerializeResponse($serialzeData);
    }

    // ---- User Routes ----

    #[Route('/api/company/{companyUuid}/invoices', name: 'get_all_invoices', methods:['GET'])]
    #[IsGranted(CompanyVoter::VIEW, 'company')]
    public function getClientAllInvoices(#[MapEntity(mapping: ['companyUuid' => 'uuid'])] ?Company $company): JsonResponse
    {
        try {

            $invoices = $this->invoiceService->getAllClientInvoices($company);
            $serialzeData = $this->serializer->serialize($invoices, 'json', ['groups' => 'get:light_invoice']);
            return $this->format->sendSuccessSerializeResponse($serialzeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }

    #[Route('/api/company/{companyUuid}/invoice/{invoiceUuid}', name: 'get_one_invoice', methods:['GET'])]
    #[IsGranted(InvoiceVoter::VIEW, 'invoice')]
    public function getClientInvoice(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] ?Company $company,
        #[MapEntity(mapping: ['invoiceUuid' => 'uuid'])] ?Invoice $invoice
    ): JsonResponse {
        try {

            $invoice = $this->invoiceService->getOneClientInvoice($company, $invoice);
            $serialzeData = $this->serializer->serialize($invoice, 'json', ['groups' => 'get:light_invoice']);
            return $this->format->sendSuccessSerializeResponse($serialzeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }

    #[Route('/api/company/{companyUuid}/invoice', name: 'create_client_invoice', methods:['POST'])]
    #[IsGranted(CompanyVoter::CREATE, 'company')]
    public function createClientInvoice(#[MapEntity(mapping: ['companyUuid' => 'uuid'])] ?Company $company, Request $request): JsonResponse
    {
        $payload = $request->getPayload()->all();

        try {

            $invoice = $this->invoiceService->createClientInvoice($company, $payload);
            $serialzeData = $this->serializer->serialize($invoice, 'json', ['groups' => 'get:light_invoice']);
            return $this->format->sendSuccessSerializeResponse($serialzeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }

    #[Route('/api/company/{companyUuid}/invoice/{invoiceUuid}', name: 'update_one_invoice', methods:['PATCH'])]
    #[IsGranted(InvoiceVoter::VIEW, 'invoice')]
    public function updateClientInvoice(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] ?Company $company,
        #[MapEntity(mapping: ['invoiceUuid' => 'uuid'])] ?Invoice $invoice,
        Request $request
    ): JsonResponse {
        $payload = $request->getPayload()->all();

        try {

            $invoice = $this->invoiceService->updateClientInvoice($company, $invoice, $payload);
            $serialzeData = $this->serializer->serialize($invoice, 'json', ['groups' => 'get:light_invoice']);
            return $this->format->sendSuccessSerializeResponse($serialzeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }

    #[Route('/api/company/{companyUuid}/invoice/{invoiceUuid}', name: 'delete_one_invoice', methods:['DELETE'])]
    #[IsGranted(InvoiceVoter::DELETE, 'invoice')]
    public function deletelientInvoice(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] ?Company $company,
        #[MapEntity(mapping: ['invoiceUuid' => 'uuid'])] ?Invoice $invoice,
    ): JsonResponse {
        try {

            $invoice = $this->invoiceService->deleteClientInvoice($company, $invoice);
            return $this->format->sendSuccessReponse(null, Response::HTTP_NO_CONTENT);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }

    #[Route('/api/company/{companyUuid}/invoices', name: 'delete_client_invoices', methods:['DELETE'])]
    #[IsGranted(CompanyVoter::CREATE, 'company')]
    public function deleteClientInvoices(#[MapEntity(mapping: ['companyUuid' => 'uuid'])] ?Company $company, Request $request): JsonResponse
    {
        $payload = $request->getPayload()->all();

        try {

            $invoice = $this->invoiceService->createClientInvoice($company, $payload);
            $serialzeData = $this->serializer->serialize($invoice, 'json', ['groups' => 'get:light_invoice']);
            return $this->format->sendSuccessSerializeResponse($serialzeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }
}
