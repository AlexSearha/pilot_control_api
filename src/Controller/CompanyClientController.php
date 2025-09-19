<?php

namespace App\Controller;

use App\Entity\Company;
use App\Security\Voter\CompanyClientVoter;
use App\Service\CompanyClientService;
use App\Service\FormatService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CompanyClientController extends AbstractController
{

    public function __construct(
        private SerializerInterface $serializer,
        private CompanyClientService $companyClientService,
        private FormatService $format

    )
    {}

    // ---- Super Admin Routes ----

    #[Route('/api/companies/clients', name: 'app_get_companies_clients', methods:['GET'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function getAllCompaniesClients(): JsonResponse
    {

        $companiesClients = $this->companyClientService->getCompaniesClients();

        $serializeData = $this->serializer->serialize($companiesClients, 'json', ['groups' => 'get:full_company']);

        return $this->format->sendSuccessSerializeResponse($serializeData);
    }

    // ---- Users Routes ----

    #[Route('/api/company/{companyUuid}/clients', name: 'app_get_company_clients', methods:['GET'])]
    #[IsGranted(CompanyClientVoter::VIEW, 'company')]
    public function getAllCompanyClients(#[MapEntity(mapping: ['userUuid' => 'uuid'])] Company $compagny): JsonResponse
    {
        try {
            $companyClients = $this->companyClientService->getCompanyClients($compagny->getUuid());
            $serializeData = $this->serializer->serialize($companyClients, 'json', ['groups' => 'get:full_company']);

            return $this->format->sendSuccessSerializeResponse($serializeData);
        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());

        }
    }

    #[Route('/api/company/{companyUuid}/client/{clientUuid}', name: 'app_get_company_client', methods:['GET'])]
    #[IsGranted(CompanyClientVoter::VIEW, 'compagny')]
    public function getCompanyClient(#[MapEntity(mapping: ['companyUuid' => 'uuid'])] Company $compagny, string $clientUuid): JsonResponse
    {
        try {
            $companyClient = $this->companyClientService->getCompanyClient($compagny->getUuid(), $clientUuid);
            $serializeData = $this->serializer->serialize($companyClient, 'json', ['groups' => 'get:full_company']);

            return $this->format->sendSuccessSerializeResponse($serializeData);
        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());

        }
    }

    #[Route('/api/company/{companyUuid}/client', name: 'app_create_company_client', methods:['POST'])]
    #[IsGranted(CompanyClientVoter::CREATE, 'compagny')]
    public function createCompanyClient(#[MapEntity(mapping: ['companyUuid' => 'uuid'])] Company $compagny, Request $request): JsonResponse
    {
        $payload = $request->getPayload()->all();

        try {
            $companyClient = $this->companyClientService->createCompanyClient($payload, $compagny->getUuid());
            $serializeData = $this->serializer->serialize($companyClient, 'json', ['groups' => 'get:full_company']);

            return $this->format->sendSuccessSerializeResponse($serializeData);
        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());

        }
    }

     #[Route('/api/company/{companyUuid}/client/{clientUuid}', name: 'app_update_company_client', methods:['PATCH'])]
    #[IsGranted(CompanyClientVoter::CREATE, 'compagny')]
    public function updateCompanyClient(#[MapEntity(mapping: ['companyUuid' => 'uuid'])] Company $compagny, string $clientUuid ,Request $request): JsonResponse
    {
        $payload = $request->getPayload()->all();

        try {
            $companyClient = $this->companyClientService->updateCompanyClient($payload, $compagny->getUuid(), $clientUuid);
            $serializeData = $this->serializer->serialize($companyClient, 'json', ['groups' => 'get:full_company']);

            return $this->format->sendSuccessSerializeResponse($serializeData);
        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());

        }
    }
}
