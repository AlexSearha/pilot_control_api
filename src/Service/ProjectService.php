<?php

namespace App\Service;

use App\Repository\ProjectRepository;
use Symfony\Component\Serializer\SerializerInterface;

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

}
