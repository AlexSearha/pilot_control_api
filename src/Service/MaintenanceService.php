<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\Item;
use App\Entity\Maintenance;
use App\Enum\MaintenanceStatusEnum;
use App\Enum\MaintenanceTypeEnum;
use App\Repository\MaintenanceRepository;
use App\Security\Voter\MaintenanceVoter;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MaintenanceService extends AbstractController
{
     private $errorsToStrigify = [];

    public function __construct(
        private CompanyService $companyService,
        private ItemService $itemService,
        private ValidatorInterface $validator,
        private EntityManagerInterface $em,
        private MaintenanceRepository $maintenanceRepo,
        private UserService $userService
    )
    {}

    public function getAllMaintenances() : array
    {
        return $this->maintenanceRepo->findBy([], ['startedAt' => "ASC"]);
    }

    public function getAllClientMaintenances(?Company $company): array
    {
        $this->companyService->isCompanyExist($company);

        return $this->maintenanceRepo->findMaintenanceByCompany($company);
    }

    public function getAllItemClientMaitenances(?Company $company, ?Item $item) : array
    {
        $this->companyService->isCompanyExist($company);
        $this->itemService->isItemExist($item);

        return $this->maintenanceRepo->findBy(['id' => $item->getId(), ['id' => 'ASC']]);
    }

    public function getClientMaintenance(?Company $company, ?Item $item, ?Maintenance $maintenance) : Maintenance
    {
        $this->companyService->isCompanyExist($company);
        $this->itemService->isItemExist($item);
        $this->isMaintenanceExist($maintenance);

        return $maintenance;
    }

    public function findMaintenanceByUuid(?Company $company, ?Item $item, string $maintenanceUuid) : Maintenance
    {
        $this->companyService->isCompanyExist($company);
        $this->itemService->isItemExist($item);

        $maintenance = $this->maintenanceRepo->findOneBy(['uuid' => $maintenanceUuid]);
        $this->isMaintenanceExist($maintenance);

        return $maintenance;
    }

    public function createClientMaintenance(?Company $company,?Item $item, array $payload) : Maintenance
    {
        $this->companyService->isCompanyExist($company);
        $this->itemService->isItemExist($item);

        if (count($payload) === 0) {
             throw new \Exception("Aucune donnée à traiter", Response::HTTP_BAD_REQUEST);
        }

        $newMaintenance = new Maintenance();
        $newMaintenance->setItem($item);

        if (isset($payload['maintenanceType'])) {
            $newMaintenance->setMaintenanceType(MaintenanceTypeEnum::from($payload['maintenanceType']));
        }

        if (isset($payload['maintenanceStatus'])) {
            $newMaintenance->setStatus(MaintenanceStatusEnum::from($payload['maintenanceStatus']));
        }

        if (isset($payload['description'])) {
            $newMaintenance->setDescription($payload['description']);
        }

        if (isset($payload['scheduledAt'])) {
            $newMaintenance->setScheduledAt(new DateTimeImmutable($payload['scheduledAt']));
        }

        if (isset($payload['startedAt'])) {
            $newMaintenance->setStartedAt(new DateTimeImmutable($payload['startedAt']));
        }

        if (isset($payload['completedAt'])) {
            $newMaintenance->setCompletedAt(new DateTimeImmutable($payload['completedAt']));
        }

        if (isset($payload['nextMaintenanceAt'])) {
            $newMaintenance->setNextMaintenanceAt(new DateTimeImmutable($payload['nextMaintenanceAt']));
        }

        if (isset($payload['performedByName'])) {
            $newMaintenance->setPerformedByName($payload['performedByName']);
        }

        if (isset($payload['performedByUser'])) {
            $user = $this->userService->getOneUser($payload['performedByUser']);
            $newMaintenance->setPerformedByUser($user);
        }

        if (isset($payload['cost'])) {
            $newMaintenance->setCost($payload['cost']);
        }

         $errors = $this->validator->validate($newMaintenance);

        if (count($errors) > 0) {

            foreach ($errors as $error) {
                $this->errorsToStrigify[] = $error->getMessage();
            }

            throw new \Exception(implode(',', $this->errorsToStrigify), Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->em->persist($newMaintenance);
            $this->em->flush();
            $this->em->refresh($newMaintenance);

            return $newMaintenance;
        } catch (\Exception $e) {
            throw new \Exception('Une erreur est survenue', Response::HTTP_BAD_REQUEST);
        }

    }

    public function updateClientMaintenance(?Company $company,?Item $item, ?Maintenance $maintenance, array $payload) : Maintenance
    {
        $this->companyService->isCompanyExist($company);
        $this->itemService->isItemExist($item);
        $this->isMaintenanceExist($maintenance);

        if (count($payload) === 0) {
             throw new \Exception("Aucune donnée à traiter", Response::HTTP_BAD_REQUEST);
        }

         if (isset($payload['maintenanceType'])) {
            $maintenance->setMaintenanceType(MaintenanceTypeEnum::from($payload['maintenanceType']));
        }

        if (isset($payload['maintenanceStatus'])) {
            $maintenance->setStatus(MaintenanceStatusEnum::from($payload['maintenanceStatus']));
        }

        if (isset($payload['description'])) {
            $maintenance->setDescription($payload['description']);
        }

        if (isset($payload['scheduledAt'])) {
            $maintenance->setScheduledAt(new DateTimeImmutable($payload['scheduledAt']));
        }

        if (isset($payload['startedAt'])) {
            $maintenance->setStartedAt(new DateTimeImmutable($payload['startedAt']));
        }

        if (isset($payload['completedAt'])) {
            $maintenance->setCompletedAt(new DateTimeImmutable($payload['completedAt']));
        }

        if (isset($payload['nextMaintenanceAt'])) {
            $maintenance->setNextMaintenanceAt(new DateTimeImmutable($payload['nextMaintenanceAt']));
        }

        if (isset($payload['performedByName'])) {
            $maintenance->setPerformedByName($payload['performedByName']);
        }

        if (isset($payload['performedByUser'])) {
            $user = $this->userService->getOneUser($payload['performedByUser']);
            $maintenance->setPerformedByUser($user);
        }

        if (isset($payload['cost'])) {
            $maintenance->setCost($payload['cost']);
        }

        $errors = $this->validator->validate($maintenance);

        if (count($errors) > 0) {

            foreach ($errors as $error) {
                $this->errorsToStrigify[] = $error->getMessage();
            }

            throw new \Exception(implode(',', $this->errorsToStrigify), Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->em->flush();
            $this->em->refresh($maintenance);
            return $maintenance;

        } catch (\Exception $e) {
            throw new \Exception('Une erreur est survenue', Response::HTTP_BAD_REQUEST);
        }
    }

    public function deleteClientMaintenance(?Company $company,?Item $item, ?Maintenance $maintenance): void
    {
        $this->companyService->isCompanyExist($company);
        $this->itemService->isItemExist($item);
        $this->isMaintenanceExist($maintenance);

        try {
            $maintenance->setDeletedAt(new DateTimeImmutable());
            $this->em->flush();
        } catch (\Exception $e) {
            throw new \Exception("Une erreur est survenue", Response::HTTP_BAD_REQUEST);

        }
    }

    public function deleteClientMaintenances(?Company $company,?Item $item, array $payload): void
    {
        $this->companyService->isCompanyExist($company);
        $this->itemService->isItemExist($item);

        foreach ($payload['maintenances'] as $maintenanceUuid) {
            $maintenance = $this->findMaintenanceByUuid($company, $item ,$maintenanceUuid);
            $this->denyAccessUnlessGranted(MaintenanceVoter::DELETE, $maintenance);
            $this->deleteClientMaintenance($company, $item, $maintenance);
        }
    }

    public function isMaintenanceExist(?Maintenance $maintenance) : void
    {
        if (!$maintenance) {
            throw new \Exception("Fournisseur inconnu", Response::HTTP_FOUND);
        }
    }

}
