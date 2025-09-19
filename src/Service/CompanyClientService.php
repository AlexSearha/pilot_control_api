<?php

namespace App\Service;

use App\Entity\CompanyClient;
use App\Repository\CompanyClientRepository;
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
        private CompanyService $companyService
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

    public function updateCompanyClient(array $payload, string $companyUuid, string $clientUuid)
    {
        $companyClient  = $this->getClientByUuid($clientUuid);
        $compagny       = $companyClient->getCompany();

        if ($companyClient->getCompany()->getUuid() !== $companyUuid) {
            throw new \Exception("Une erreur est survenue", Response::HTTP_BAD_REQUEST);
        }

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

        if (isset($payload['company'])) {

            if (!in_array('ROLE_SUPER_ADMIN', $this->getUser()->getRoles())) {
                throw new \Exception("Vous n'avez pas les droits pour effectuer cette action", Response::HTTP_UNAUTHORIZED);

            }
            $companyClient->setCompany($compagny);
        }

        // TODO: Penser à ajouter le set pour CompanyType

    }

    public function getClientByUuid(string $clientUuid) : CompanyClient
    {
        $client = $this->companyClientRepo->findOneBy(['uuid' => $clientUuid]);
        if (!$client) {
            throw new \Exception("Client inconnu", Response::HTTP_NOT_FOUND);

        }
        return $client;
    }
}
