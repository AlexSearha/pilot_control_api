<?php

namespace App\Controller;

use App\Entity\Company;
use App\Security\Voter\CompanyVoter;
use App\Service\FormatService;
use App\Service\ProjectService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

final class ProjectController extends AbstractController
{
    public function __construct(
        private ProjectService $projectService,
        private SerializerInterface $serializer,
        private FormatService $format
    )
    {}

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
    public function getOneProjects(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] Company $company
    ): JsonResponse
    {
        $projects = $this->projectService->getAllProjects($company->getUuid());

        $serializeData = $this->serializer->serialize($projects, 'json', ['groups' => 'get:light_project']);

        return $this->format->sendSuccessSerializeResponse($serializeData);
    }

}
