<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Project;
use App\Security\Voter\CompanyVoter;
use App\Security\Voter\ProjectVoter;
use App\Service\FormatService;
use App\Service\ProjectService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

final class ProjectController extends AbstractController
{
    public function __construct(
        private ProjectService $projectService,
        private SerializerInterface $serializer,
        private FormatService $format
    ) {
    }

    // ---- Super Admin Routes ----

    #[Route('/api/projects', name: 'project_get_all', methods: ['GET'])]
    #[IsGranted('ROLE_SUPER_ADMIN', null, "ACCES REFUSE, vous n'avez pas les droits pour éffectuer cette action")]
    public function getProjects(): JsonResponse
    {
        $projects = $this->projectService->getAllProjects();

        $serializeData = $this->serializer->serialize($projects, 'json', ['groups' => 'get:light_project']);

        return $this->format->sendSuccessSerializeResponse($serializeData);
    }

    // ---- Users Routes ----

    #[Route('/api/company/{companyUuid}/projects', name: 'project_get_company_projects', methods:['GET'])]
    #[IsGranted(CompanyVoter::VIEW, 'company')]
    public function getClientProjects(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] Company $company
    ): JsonResponse {
        $projects = $this->projectService->getClientProjects($company->getUuid());

        $serializeData = $this->serializer->serialize($projects, 'json', ['groups' => 'get:light_project']);

        return $this->format->sendSuccessSerializeResponse($serializeData);
    }

    #[Route('/api/company/{companyUuid}/project/{projectUuid}', name: 'project_get_company_project', methods:['GET'])]
    #[IsGranted(ProjectVoter::VIEW, 'project')]
    public function getOneProject(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] Company $company,
        #[MapEntity(mapping: ['projectUuid' => 'uuid'])] Project $project
    ): JsonResponse {
        $project = $this->projectService->getAllProjects($company->getUuid());

        $serializeData = $this->serializer->serialize($project, 'json', ['groups' => 'get:light_project']);

        return $this->format->sendSuccessSerializeResponse($serializeData);
    }

    #[Route('/api/company/{companyUuid}/project', name: 'project_create_company_project', methods:['POST'])]
    #[IsGranted(CompanyVoter::CREATE, 'company')]
    public function createProject(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] Company $company,
        Request $request
    ): JsonResponse {
        $payload = $request->getPayload()->all();

        try {

            $project = $this->projectService->createProject($company, $payload);
            $serializeData = $this->serializer->serialize($project, 'json', ['groups' => 'get:light_project']);

            return $this->format->sendSuccessSerializeResponse($serializeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());

        }
    }

    #[Route('/api/company/{companyUuid}/project/{projectUuid}', name: 'project_update_company_project', methods:['PATCH'])]
    #[IsGranted(ProjectVoter::EDIT, 'project')]
    public function updateProject(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] Company $company,
        #[MapEntity(mapping: ['projectUuid' => 'uuid'])] Project $project,
        Request $request
    ): JsonResponse {
        $payload = $request->getPayload()->all();

        try {

            $project = $this->projectService->updateProject($company, $project, $payload);
            $serializeData = $this->serializer->serialize($project, 'json', ['groups' => 'get:light_project']);

            return $this->format->sendSuccessSerializeResponse($serializeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());

        }
    }

    #[Route('/api/company/{companyUuid}/project/{projectUuid}', name: 'project_delete_company_project', methods:['DELETE'])]
    #[IsGranted(ProjectVoter::DELETE, 'project')]
    public function deleteProject(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] Company $company,
        #[MapEntity(mapping: ['projectUuid' => 'uuid'])] Project $project,
    ): JsonResponse {
        try {

            $this->projectService->deleteProject($company, $project);
            return $this->format->sendSuccessReponse(null, Response::HTTP_NO_CONTENT);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());

        }
    }

}
