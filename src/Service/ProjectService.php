<?php

namespace App\Service;

use App\Repository\ProjectRepository;

class ProjectService
{
    public function __construct(
        private ProjectRepository $projectRepo
    )
    {}

    public function getAllProjects()
    {
        return $this->projectRepo->findBy([], ['company' => 'ASC']);
    }

    public function getClientProject(string $companyUuid)
    {
        return $this->projectRepo->findBy(['company' =>  $companyUuid ], ['name' => 'ASC']);
    }

}
