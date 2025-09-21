<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\CompanyClient;
use App\Repository\CompanyClientRepository;
use App\Security\Voter\CompanyClientVoter;
use Cocur\Slugify\Slugify;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class CompanyClientService extends AbstractController
{

    private $errorToStringify = [];


    public function __construct(
        private EntityManagerInterface $em,
        private CompanyClientRepository $companyClientRepo,
        private ValidatorInterface $validator,
        private CompanyService $companyService,
    )
    {}


    public function getCompaniesClients(): array
    {
        return $this->companyClientRepo->findBy([], ['name' => 'ASC']);

    }

    public function getCompanyClients(string $companyUuid): array
    {
        $compagny = $this->companyService->getCompanyByUuid($companyUuid);

        return $this->companyClientRepo->findBy(['company' => $compagny->getId()], ['name' => 'ASC']);

    }

    public function getCompanyClient(string $companyUuid, string $clientUuid): object
    {
        $compagny = $this->companyService->getCompanyByUuid($companyUuid);

        return $this->companyClientRepo->findOneBy(['uuid' => $clientUuid, 'id' => $compagny->getId()], ['name' => 'ASC']);

    }

    public function createCompanyClient(array $payload, string $companyUuid): CompanyClient
    {
        $company = $this->companyService->getCompanyByUuid($companyUuid);

        $newCompanyClient = new CompanyClient();

        if (isset($payload['name'])) {
            $newCompanyClient->setName($payload['name']);
        }
        if (isset($payload['email'])) {
            $findCompanyClient = $this->companyClientRepo->findOneBy(['email' => $payload['email']]);
            if ($findCompanyClient) {
                throw new \Exception("Le client existe déja", Response::HTTP_BAD_REQUEST);

            }

            $newCompanyClient->setEmail($payload['email']);
        }
        if (isset($payload['address'])) {
            $newCompanyClient->setAddress($payload['address']);
        }
        if (isset($payload['zipcode'])) {
            $newCompanyClient->setZipcode($payload['zipcode']);
        }
        if (isset($payload['city'])) {
            $newCompanyClient->setCity($payload['city']);
        }
        if (isset($payload['siret'])) {
            $newCompanyClient->setSiret($payload['siret']);
        }
        if (isset($payload['siren'])) {
            $newCompanyClient->setSiren($payload['siren']);
        }
        if (isset($payload['region'])) {
            $newCompanyClient->setRegion($payload['region']);
        }
        if (isset($payload['vatNumber'])) {
            $newCompanyClient->setVatNumber($payload['vatNumber']);
        }
        if (isset($payload['activityType'])) {
            $newCompanyClient->setActiveTrue($payload['activityType']);
        }
        if (isset($payload['website'])) {
            $newCompanyClient->setWebsite($payload['website']);
        }
        if (isset($payload['active'])) {
            $newCompanyClient->setWebsite($payload['active']);
        }

        $newCompanyClient->setCompany($company);

        // TODO: besoin de completer avec les CompanyType


        $errors = $this->validator->validate($newCompanyClient);

        if (count($errors) > 0) {

            foreach ($errors as $error) {
                $this->errorToStringify[] = $error->getMessage();
            }

            throw new \Exception(implode(',', $this->errorToStringify), Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->em->persist($newCompanyClient);
            $this->em->flush();
            $this->em->refresh($newCompanyClient);

            return $newCompanyClient;

        } catch (\Exception $e) {

            throw new \Exception($e->getMessage(), $e->getCode());

        }
    }

    public function updateCompanyClient(array $payload, Company|null $company, CompanyClient|null $companyClient)
    {
        $this->isCompanyClientExist($companyClient);
        $this->companyService->isCompanyExist($company);
        $slugify = new Slugify();

        if (isset($payload['name'])) {
            $companyClient->setName($payload['name']);
        }
        if (isset($payload['email'])) {
            $companyClient->setEmail($payload['email']);
        }
        if (isset($payload['address'])) {
            $companyClient->setAddress($payload['address']);
        }
        if (isset($payload['zipcode'])) {
            $companyClient->setZipcode($payload['zipcode']);
        }
        if (isset($payload['city'])) {
            $companyClient->setCity($payload['city']);
        }
        if (isset($payload['siret'])) {
            $companyClient->setSiret($payload['siret']);
        }
        if (isset($payload['siren'])) {
            $companyClient->setSiren($payload['siren']);
        }
        if (isset($payload['region'])) {
            $companyClient->setRegion($payload['region']);
        }
        if (isset($payload['vatNumber'])) {
            $companyClient->setVatNumber($payload['vatNumber']);
        }
        if (isset($payload['activityType'])) {
            $companyClient->setActiveTrue($payload['activityType']);
        }
        if (isset($payload['website'])) {
            $companyClient->setWebsite($payload['website']);
        }
        if (isset($payload['active'])) {
            $companyClient->setWebsite($payload['active']);
        }
        if (isset($payload['slug'])) {
            if (!in_array('ROLE_SUPER_ADMIN', $this->getUser()->getRoles())) {
                throw new \Exception("Vous n'avez pas les droits pour modifier le slug", Response::HTTP_UNAUTHORIZED);

            }
            $companyClient->setSlug($slugify->slugify($payload['slug']));
        }

        if (isset($payload['company'])) {

            if (!in_array('ROLE_SUPER_ADMIN', $this->getUser()->getRoles())) {
                throw new \Exception("Vous n'avez pas les droits pour modifier la société", Response::HTTP_UNAUTHORIZED);

            }
            $companyClient->setCompany($company);
        }

         $errors = $this->validator->validate($companyClient);

        if (count($errors) > 0) {

            foreach ($errors as $error) {
                $this->errorToStringify[] = $error->getMessage();
            }

            throw new \Exception(implode(',', $this->errorToStringify), Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->em->flush();
            $this->em->refresh($companyClient);
            return $companyClient;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }

    }

    public function deleteOneCompanyClient(Company|null $company, CompanyClient|null $companyClient): void
    {
        $this->isCompanyClientExist($companyClient);
        $this->companyService->isCompanyExist($company);

        $companyClient->setDeletedAt(new DateTimeImmutable());

        try {
            $this->em->flush();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }

    }

    public function isCompanyClientExist(CompanyClient|null $companyClient)
    {
        if (!$companyClient) {
            throw new \Exception("Client inconnu", Response::HTTP_NOT_FOUND);
        }

        return $companyClient;
    }

    public function deleteCompanyClients(array $payload, Company|null $company)
    {
        if (!isset($payload['companyClientsUuids'])) {
            throw new \Exception("Aucune donnée à traiter", Response::HTTP_BAD_REQUEST);
        }

        $this->companyService->isCompanyExist($company);

        foreach ($payload['companyClientsUuids'] as $companyClientUuid) {
           $companyClient = $this->getClientByUuid($companyClientUuid);

           $this->denyAccessUnlessGranted(CompanyClientVoter::DELETE, $companyClient,  "ACCES REFUSE, vous n'avez pas les droits pour effectuer effectuer cette action");

           $companyClient->setDeletedAt(new DateTimeImmutable());

        }

        try {
            $this->em->flush();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    public function getClientByUuid(string $companyClientUuid) : CompanyClient
    {
        $client = $this->companyClientRepo->findOneBy(['uuid' => $companyClientUuid]);
        if (!$client) {
            throw new \Exception("Client inconnu", Response::HTTP_NOT_FOUND);

        }
        return $client;
    }
}
