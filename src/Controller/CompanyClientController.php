<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\CompanyClient;
use App\Security\Voter\CompanyClientVoter;
use App\Security\Voter\CompanyVoter;
use App\Service\CompanyClientService;
use App\Service\FormatService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CompanyClientController extends AbstractController
{
    public function __construct(
        private SerializerInterface $serializer,
        private CompanyClientService $companyClientService,
        private FormatService $format
    ) {
    }

    // ---- Super Admin Routes ----

    #[Route('/api/companies/clients', name: 'app_get_companies_clients', methods:['GET'])]
    #[IsGranted('ROLE_SUPER_ADMIN', null, "Accès refusé, vous n'avez pas les droits pour effectuer cette action")]
    public function getAllCompaniesClients(): JsonResponse
    {

        $companiesClients = $this->companyClientService->getCompaniesClients();

        $serializeData = $this->serializer->serialize($companiesClients, 'json', ['groups' => 'get:full_company']);

        return $this->format->sendSuccessSerializeResponse($serializeData);
    }

    // ---- Users Routes ----

    #[Route('/api/company/{companyUuid}/clients', name: 'app_get_company_clients', methods:['GET'])]
    #[IsGranted(CompanyVoter::LIST_ALL, 'company')]
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
    public function getCompanyClient(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] Company $compagny,
        #[MapEntity(mapping: ['clientUuid' => 'uuid'])] CompanyClient $compagnyClient,
    ): JsonResponse {
        try {
            $companyClient = $this->companyClientService->getCompanyClient($compagny->getUuid(), $compagnyClient->getUuid());
            $serializeData = $this->serializer->serialize($companyClient, 'json', ['groups' => 'get:full_company']);

            return $this->format->sendSuccessSerializeResponse($serializeData);
        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());

        }
    }

    #[Route('/api/company/{companyUuid}/client', name: 'app_create_company_client', methods:['POST'])]
    #[IsGranted(CompanyVoter::CREATE, 'compagny')]
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
    #[IsGranted(CompanyClientVoter::EDIT, 'compagnyClient')]
    public function updateCompanyClient(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] ?Company $compagny,
        #[MapEntity(mapping: ['clientUuid' => 'uuid'])] ?CompanyClient $compagnyClient,
        Request $request
    ): JsonResponse {
        $payload = $request->getPayload()->all();

        try {
            $companyClient = $this->companyClientService->updateCompanyClient($payload, $compagny, $compagnyClient);
            $serializeData = $this->serializer->serialize($companyClient, 'json', ['groups' => 'get:full_company']);

            return $this->format->sendSuccessSerializeResponse($serializeData);
        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());

        }
    }

    #[Route('/api/company/{companyUuid}/client/{clientUuid}', name: 'app_delete_company_client', methods:['DELETE'])]
    #[IsGranted(CompanyClientVoter::DELETE, 'compagnyClient')]
    public function deleteCompanyClient(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] ?Company $compagny,
        #[MapEntity(mapping: ['clientUuid' => 'uuid'])] ?CompanyClient $compagnyClient,
    ): JsonResponse {
        try {
            $this->companyClientService->deleteOneCompanyClient($compagny, $compagnyClient);

            return $this->format->sendSuccessReponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());

        }
    }

    #[Route('/api/company/{companyUuid}/clients', name: 'app_delete_company_clients', methods:['DELETE'])]
    #[IsGranted('ROLE_MANAGER', null, "Accès refusé, vous n'avez pas les droits pour effectuer cette action")]
    public function deleteCompanyClients(#[MapEntity(mapping: ['companyUuid' => 'uuid'])] ?Company $compagny, Request $request): JsonResponse
    {
        $payload = $request->getPayload()->all();

        try {
            $this->companyClientService->deleteCompanyClients($payload, $compagny);

            return $this->format->sendSuccessReponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());

        }
    }
}
