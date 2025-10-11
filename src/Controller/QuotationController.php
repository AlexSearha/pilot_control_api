<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Quotation;
use App\Security\Voter\CompanyVoter;
use App\Security\Voter\QuotationVoter;
use App\Service\FormatService;
use App\Service\QuotationService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

final class QuotationController extends AbstractController
{
    public function __construct(
        private QuotationService $quotationService,
        private SerializerInterface $serializer,
        private FormatService $format
    ) {
    }

    // ---- Super Admin Routes ----

    #[Route('/api/quotations', name: 'get_all_quotations', methods:['GET'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function getAllQuotations(): JsonResponse
    {
        $quotations = $this->quotationService->getAllQuotations();
        $serialzeData = $this->serializer->serialize($quotations, 'json', ['groups' => 'get:light_quotation']);
        return $this->format->sendSuccessSerializeResponse($serialzeData);
    }

    // ---- Super User Routes ----

    #[Route('/api/company/{companyUuid}/quotations', name: 'get_all_quotations', methods:['GET'])]
    #[IsGranted(CompanyVoter::VIEW, 'company')]
    public function getClientAllQuotations(#[MapEntity(mapping: ['companyUuid' => 'uuid'])] ?Company $company): JsonResponse
    {
        try {

            $quotations = $this->quotationService->getClientQuotations($company);
            $serialzeData = $this->serializer->serialize($quotations, 'json', ['groups' => 'get:light_quotation']);
            return $this->format->sendSuccessSerializeResponse($serialzeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }

    #[Route('/api/company/{companyUuid}/quotation/{quotationUuid}', name: 'get_client_quotation', methods:['GET'])]
    #[IsGranted(QuotationVoter::VIEW, 'quotation')]
    public function getOneClientQuotation(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] ?Company $company,
        #[MapEntity(mapping: ['quotationUuid' => 'uuid'])] ?Quotation $quotation,
    ): JsonResponse {
        try {

            $quotation = $this->quotationService->getOneClientQuotation($company, $quotation);
            $serialzeData = $this->serializer->serialize($quotation, 'json', ['groups' => 'get:light_quotation']);
            return $this->format->sendSuccessSerializeResponse($serialzeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }

    #[Route('/api/company/{companyUuid}/quotation', name: 'create_client_quotation', methods:['POST'])]
    #[IsGranted(CompanyVoter::CREATE, 'company')]
    public function createClientQuotation(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] ?Company $company,
        Request $request
    ): JsonResponse {

        $payload = $request->getPayload()->all();

        try {

            $quotations = $this->quotationService->createClientQuotation($company, $payload);
            $serialzeData = $this->serializer->serialize($quotations, 'json', ['groups' => 'get:light_quotation']);
            return $this->format->sendSuccessSerializeResponse($serialzeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }

    #[Route('/api/company/{companyUuid}/quotation/{quotationUuid}', name: 'update_client_quotation', methods:['PATCH'])]
    #[IsGranted(QuotationVoter::EDIT, 'quotation')]
    public function updateClientQuotation(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] ?Company $company,
        #[MapEntity(mapping: ['quotationUuid' => 'uuid'])] ?Quotation $quotation,
        Request $request
    ): JsonResponse {
        $payload = $request->getPayload()->all();

        try {

            $updateQuotation = $this->quotationService->updateClientQuotation($company, $quotation, $payload);
            $serialzeData = $this->serializer->serialize($updateQuotation, 'json', ['groups' => 'get:light_quotation']);
            return $this->format->sendSuccessSerializeResponse($serialzeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }

    #[Route('/api/company/{companyUuid}/quotation/{quotationUuid}', name: 'delete_client_quotation', methods:['DELETE'])]
    #[IsGranted(QuotationVoter::DELETE, 'quotation')]
    public function deleteClientQuotation(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] ?Company $company,
        #[MapEntity(mapping: ['quotationUuid' => 'uuid'])] ?Quotation $quotation,
    ): JsonResponse {
        try {

            $updateQuotation = $this->quotationService->deleteClientQuotation($company, $quotation);
            $serialzeData = $this->serializer->serialize($updateQuotation, 'json', ['groups' => 'get:light_quotation']);
            return $this->format->sendSuccessSerializeResponse($serialzeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }

    #[Route('/api/company/{companyUuid}/quotations', name: 'delete_client_quotations', methods:['DELETE'])]
    #[IsGranted(QuotationVoter::DELETE, 'quotation')]
    public function deleteClientQuotations(#[MapEntity(mapping: ['companyUuid' => 'uuid'])] ?Company $company, Request $request): JsonResponse
    {

        $payload = $request->getPayload()->all();

        try {

            $updateQuotation = $this->quotationService->deleteClientQuotations($company, $payload);
            $serialzeData = $this->serializer->serialize($updateQuotation, 'json', ['groups' => 'get:light_quotation']);
            return $this->format->sendSuccessSerializeResponse($serialzeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }
}
