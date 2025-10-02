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
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

final class QuotationController extends AbstractController
{
    public function __construct(
        private QuotationService $quotationService,
        private SerializerInterface $serializer,
        private FormatService $format
    ) {}

    // ---- Super Admin Routes ----

    #[Route('/api/quotations', name: 'app_get_all_quotations', methods:['GET'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function getAllQuotations(): JsonResponse
    {
        $quotations = $this->quotationService->getAllQuotations();
        $serialzeData = $this->serializer->serialize($quotations, 'json', ['groups' => 'get:light_quotation']);
        return $this->format->sendSuccessSerializeResponse($serialzeData);
    }

    // ---- Super User Routes ----

    #[Route('/api/company/{companyUuid}/quotations', name: 'item_get_all_quotations', methods:['GET'])]
    #[IsGranted(CompanyVoter::VIEW, 'company')]
    public function getClientAllQuotations(#[MapEntity(mapping: ['companyUuid' => 'uuid'])] Company $company): JsonResponse
    {
        try {

            $quotations = $this->quotationService->getClientQuotations($company);
            $serialzeData = $this->serializer->serialize($quotations, 'json', ['groups' => 'get:light_quotation']);
            return $this->format->sendSuccessSerializeResponse($serialzeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }

    #[Route('/api/company/{companyUuid}/quotation/{quotationUuid}', name: 'item_get_client_quotation', methods:['GET'])]
    #[IsGranted(QuotationVoter::VIEW, 'quotation')]
    public function getOneClientQuotation(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] Company $company,
        #[MapEntity(mapping: ['quotationUuid' => 'uuid'])] Quotation $quotation,
        ): JsonResponse
    {
        try {

            $quotation = $this->quotationService->getOneClientQuotation($company, $quotation);
            $serialzeData = $this->serializer->serialize($quotation, 'json', ['groups' => 'get:light_quotation']);
            return $this->format->sendSuccessSerializeResponse($serialzeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }
}
