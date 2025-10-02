<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Security\Voter\ProjectVoter;
use Cocur\Slugify\Slugify;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProjectService extends AbstractController
{
    private $errorsToStrigify = [];

    public function __construct(
        private ProjectRepository $projectRepo,
        private CompanyService $companyService,
        private EntityManagerInterface $em,
        private UserService $userService,
        private ValidatorInterface $validator
    )
    {}

    public function getAllProjects()
    {
        return $this->projectRepo->findBy([], ['company' => 'ASC']);
    }

    public function getClientProjects(string $companyUuid)
    {
        return $this->projectRepo->findBy(['company' =>  $companyUuid ], ['name' => 'ASC']);
    }

    public function getOneClientProject(Project $project)
    {
        $this->isPojectExist($project);

        return $this->projectRepo->findOneBy(['uuid' => $project->getUuid()]);
    }

    public function createProject(Company|null $company, array $payload)
    {
        $this->companyService->isCompanyExist($company);
        $slugify = new Slugify();
        $newProject = new Project();

        $newProject->setcompany($company);

        if (isset($payload['name'])) {
            $newProject->setName($payload['name']);
            $newProject->setSlug($slugify->slugify($payload['name']));
        }
        if (isset($payload['address'])) {
            $newProject->setAddress($payload['address']);
        }
        if (isset($payload['zipcode'])) {
            $newProject->setZipcode($payload['zipcode']);
        }
        if (isset($payload['city'])) {
            $newProject->setCity($payload['city']);
        }
        if (isset($payload['region'])) {
            $newProject->setRegion($payload['region']);
        }
        if (isset($payload['country'])) {
            $newProject->setCountry($payload['country']);
        }
        if (isset($payload['startDate'])) {
            $newProject->setStartDate(new DateTimeImmutable($payload['startDate']));
        }
        if (isset($payload['endDate'])) {
            $newProject->setEndDate(new DateTimeImmutable($payload['endDate']));
        }
        if (isset($payload['status'])) {
            $newProject->setStatus($payload['status']);
        }
        if (isset($payload['budget'])) {
            $newProject->setBudget($payload['budget']);
        }
        if (isset($payload['userUuids'])) {
            $newProject->addUser($this->getUser());

            foreach ($payload['userUuids'] as $user) {

                $findUser = $this->userService->getOneUser($user);
                $this->denyAccessUnlessGranted(ProjectVoter::VIEW, $newProject);

                $newProject->addUser($findUser);
            }
        }

        // TODO: ajouter les quotations une fois qu'elles seront créées
        // TODO: ajouter les invoices une fois qu'elles seront créées

         $errors = $this->validator->validate($newProject);

         if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->errorsToStrigify[] = $error->getMessage();
            }

            throw new \Exception(implode(",", $this->errorsToStrigify), Response::HTTP_BAD_REQUEST);
         }

        try {
            $this->em->persist($newProject);
            $this->em->flush();
            $this->em->refresh($newProject);

            return $newProject;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }

    }

    public function updateProject(Company|null $company, Project|null $project, array $payload)
    {
        $this->companyService->isCompanyExist($company);
        $this->isPojectExist($project);
        $slugify = new Slugify();

        if (isset($payload['name'])) {
            $project->setName($payload['name']);
            $project->setSlug($slugify->slugify($payload['name']));
        }

        if (isset($payload['address'])) {
            $project->setAddress($payload['address']);
        }
        if (isset($payload['zipcode'])) {
            $project->setZipcode($payload['zipcode']);
        }
        if (isset($payload['city'])) {
            $project->setCity($payload['city']);
        }
        if (isset($payload['region'])) {
            $project->setRegion($payload['region']);
        }
        if (isset($payload['country'])) {
            $project->setCountry($payload['country']);
        }
        if (isset($payload['startDate'])) {
            $project->setStartDate(new DateTimeImmutable($payload['startDate']));
        }
        if (isset($payload['endDate'])) {
            $project->setEndDate(new DateTimeImmutable($payload['endDate']));
        }
        if (isset($payload['status'])) {
            $project->setStatus($payload['status']);
        }
        if (isset($payload['budget'])) {
            $project->setBudget($payload['budget']);
        }
        if (isset($payload['addUsersUuids'])) {

            foreach ($payload['addUsersUuids'] as $user) {

                $findUser = $this->userService->getOneUser($user);
                $this->denyAccessUnlessGranted(ProjectVoter::VIEW, $project);

                $project->addUser($findUser);
            }
        }
        if (isset($payload['removeUsersUuids'])) {

            foreach ($payload['removeUsersUuids'] as $user) {

                $findUser = $this->userService->getOneUser($user);
                $this->denyAccessUnlessGranted(ProjectVoter::VIEW, $project);

                $project->removeUser($findUser);
            }
        }

        // TODO: Ajouter la gestion des add et remove de invoices une fois que ce sera implanté
        // TODO: Ajouter la gestion des add et remove de Quotations une fois que ce sera implanté

        $errors = $this->validator->validate($project);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->errorsToStrigify[] = $error->getMessage();
            }

            throw new \Exception(implode(",", $this->errorsToStrigify), Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->em->flush();
            $this->em->refresh($project);
            return $project;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());

        }
    }

    public function deleteProject(Company|null $company, Project|null $project): void
    {
        $this->isPojectExist($project);
        $this->companyService->isCompanyExist($company);

        $project->setDeletedAt(new DateTimeImmutable());

         try {
            $this->em->flush();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    public function isPojectExist(Project $project) :Project
    {
        if (!$project) {
            throw new \Exception("Projet inconnu", Response::HTTP_FOUND);
        }

        return $project;
    }


}
