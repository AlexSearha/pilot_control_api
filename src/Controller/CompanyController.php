<?php

namespace App\Controller;

use App\Entity\Company;
use App\Security\Voter\CompanyVoter;
use App\Service\CompanyService;
use App\Service\FormatService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

final class CompanyController extends AbstractController
{

    public function __construct(
        private CompanyService $companyService,
        private SerializerInterface $serializer,
        private FormatService $format
    )
    {}

    // ---- Super Admin Routes ----

    #[Route('/api/companies', name: 'app_get_companies', methods:['GET'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function getCompanies(): JsonResponse
    {
        $allCompanies = $this->companyService->getAllCompanies();

        $serializeData = $this->serializer->serialize($allCompanies, 'json', ['groups' => 'get:light_company']);

        return $this->format->sendSuccessSerializeResponse($serializeData);

    }

    #[Route('/api/company', name: 'app_create_company', methods:['POST'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function createCompanies(Request $request) :JsonResponse
    {
        $payload = $request->getPayload()->all();

        try {
            $allCompanies = $this->companyService->createCompany($payload);
            $serializeData = $this->serializer->serialize($allCompanies, 'json', ['groups' => 'get:light_company']);

            return $this->format->sendSuccessSerializeResponse($serializeData, Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }

    #[Route('/api/company/{companyUuid}', name: 'app_delete_company', methods:['DELETE'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function deleteCompany(string $companyUuid) :JsonResponse
    {
        try {
            $this->companyService->deleteCompany($companyUuid);

            return $this->format->sendSuccessReponse(null, Response::HTTP_NO_CONTENT);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }

    // ---- Users Routes ----

    #[Route('/api/company/{companyUuid}', name: 'app_get_one_company', methods:['GET'])]
    #[IsGranted(CompanyVoter::VIEW, 'company')]
    public function getOneCompany(#[MapEntity(mapping: ['companyUuid' => 'uuid'])] Company $company) : JsonResponse
    {
        $allCompanies = $this->companyService->getOneCompany($company->getUuid());

        $serializeData = $this->serializer->serialize($allCompanies, 'json', ['groups' => 'get:light_company']);

        return $this->format->sendSuccessSerializeResponse($serializeData);

    }

    #[Route('/api/company/{companyUuid}', name: 'app_update_one_company', methods:['PATCH'])]
    #[IsGranted(CompanyVoter::EDIT, 'company')]
    public function updateOneCompany(#[MapEntity(mapping: ['companyUuid' => 'uuid'])] Company $company, Request $request) : JsonResponse
    {

        $payload = $request->getPayload()->all();

        try {
            $company = $this->companyService->updateOneCompany($payload, $company->getUuid());

            $serializeData = $this->serializer->serialize($company, 'json', ['groups' => 'get:light_company']);

            return $this->format->sendSuccessSerializeResponse($serializeData);
        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());

        }

    }

}
