<?php

namespace App\Service;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CompanyService extends AbstractController
{

    private $errorsToStrigify = [];

    public function __construct(
        private EntityManagerInterface $em,
        private CompanyRepository $companyRepo,
        private ValidatorInterface $validator
    )
    {}

    public function getAllCompanies()
    {
        return $this->companyRepo->findAll();
    }

    public function getOneCompany(string $uuid): Company
    {
        if (!$uuid) {
            throw new \Exception("aucun identifiant reçu", Response::HTTP_BAD_REQUEST);
        }

        $company = $this->companyRepo->findOneBy(['uuid' => $uuid]);
        if (!$company) {
            throw new \Exception("Société inconnue", Response::HTTP_NOT_FOUND);
        }

        return $company;
    }

    public function createCompany(array $payload): Company
    {
        $newCompny = new Company();

        if (isset($payload['name'])) {
            $newCompny->setName($payload['name']);
        }
        if (isset($payload['email'])) {

            $findEmail = $this->companyRepo->findOneBy(['email' => $payload['email']]);
            if ($findEmail) {
                throw new \Exception("L'email renseigné représente déjà une société", Response::HTTP_BAD_REQUEST);

            }
            $newCompny->setEmail($payload['email']);
        }
        if (isset($payload['address'])) {
            $newCompny->setAddress($payload['address']);
        }
        if (isset($payload['zipcode'])) {
            $newCompny->setZipcode($payload['zipcode']);
        }
        if (isset($payload['city'])) {
            $newCompny->setCity($payload['city']);
        }
        if (isset($payload['siret'])) {
            $newCompny->setSiret($payload['siret']);
        }
        if (isset($payload['region'])) {
            $newCompny->setRegion($payload['region']);
        }
        if (isset($payload['vatNumber'])) {
            $newCompny->setVatNumber($payload['vatNumber']);
        }
        if (isset($payload['activityType'])) {
            $newCompny->setActivityType($payload['activityType']);
        }
        if (isset($payload['website'])) {
            $newCompny->setWebsite($payload['website']);
        }
        if (isset($payload['numberOfEmployee'])) {
            $newCompny->setNumberOfEmployee($payload['numberOfEmployee']);
        }
        if (isset($payload['industry'])) {
            $newCompny->setIndustry($payload['industry']);
        }
        if (isset($payload['active'])) {
            $newCompny->setActive($payload['active']);
        }
        if (isset($payload['comments'])) {
            $newCompny->setComments($payload['comments']);
        }

        $errors = $this->validator->validate($newCompny);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->errorsToStrigify[] = $error->getMessage();
            }

            throw new \Exception(implode(',', $this->errorsToStrigify),Response::HTTP_BAD_REQUEST);

        }

        try {
            $this->em->persist($newCompny);
            $this->em->flush();
            $this->em->refresh($newCompny);

            return $newCompny;

        } catch (\Exception $e) {
            throw new \Exception('Une erreur est survenue', $e->getCode());
        }
    }

    public function deleteCompany(string $companyUuid): void
    {
        $company = $this->companyRepo->findOneBy(['uuid' => $companyUuid]);
        if (!$company) {
            throw new \Exception("Société inconnue", Response::HTTP_NOT_FOUND);
        }

        /** @var Company $company */
        $company->setDeletedAt(new DateTimeImmutable());

        try {
            $this->em->flush();
            return;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());

        }
    }

    public function updateOneCompany(array $payload, string $companyUuid): Company
    {
        $company = $this->getOneCompany($companyUuid);

        if (isset($payload['name'])) {
            $company->setName($payload['name']);
        }
        if (isset($payload['email'])) {

            if (!in_array('ROLE_SUPER_ADMIN',$this->getUser()->getRoles())) {
                throw new \Exception("Vous n'avez pas les droits pour modifier l'adresse email", Response::HTTP_UNAUTHORIZED);

            }

            $company->setEmail($payload['email']);
        }
        if (isset($payload['address'])) {
            $company->setAddress($payload['address']);
        }
        if (isset($payload['zipcode'])) {
            $company->setZipcode($payload['zipcode']);
        }
        if (isset($payload['city'])) {
            $company->setCity($payload['city']);
        }
        if (isset($payload['siret'])) {
            $company->setSiret($payload['siret']);
        }
        if (isset($payload['region'])) {
            $company->setRegion($payload['region']);
        }
        if (isset($payload['vatNumber'])) {
            $company->setVatNumber($payload['vatNumber']);
        }
        if (isset($payload['activityType'])) {
            $company->setActivityType($payload['activityType']);
        }
        if (isset($payload['website'])) {
            $company->setWebsite($payload['website']);
        }
        if (isset($payload['numberOfEmployee'])) {
            $company->setNumberOfEmployee($payload['numberOfEmployee']);
        }
        if (isset($payload['industry'])) {
            $company->setIndustry($payload['industry']);
        }
        if (isset($payload['active'])) {
            $company->setActive($payload['active']);
        }
        if (isset($payload['comments'])) {
            $company->setComments($payload['comments']);
        }

        $errors = $this->validator->validate($company);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->errorsToStrigify[] = $error->getMessage();
            }

            throw new \Exception(implode(',', $this->errorsToStrigify),Response::HTTP_BAD_REQUEST);

        }

        try {
            $this->em->flush();
            $this->em->refresh($company);

            return $company;

        } catch (\Exception $e) {

            throw new \Exception('Une erreur est survenue', $e->getCode());

        }
    }
}
