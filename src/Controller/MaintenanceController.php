<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Item;
use App\Entity\Maintenance;
use App\Security\Voter\CompanyVoter;
use App\Security\Voter\MaintenanceVoter;
use App\Service\FormatService;
use App\Service\MaintenanceService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

final class MaintenanceController extends AbstractController
{
    public function __construct(
        private MaintenanceService $maintenanceService,
        private SerializerInterface $serializer,
        private FormatService $format
    ) {}

    // ---- Super Admin Routes ----

    #[Route('/api/maintenance', name: 'get_all_maintenances', methods:['GET'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function getAllMaintenances(): JsonResponse
    {
        $maintenances = $this->maintenanceService->getAllMaintenances();
        $serialzeData = $this->serializer->serialize($maintenances, 'json', ['groups' => 'get:light_maintenance']);
        return $this->format->sendSuccessSerializeResponse($serialzeData);
    }

    // ---- User Routes ----

    #[Route('/api/company/{companyUuid}/maintenances', name: 'get_all_client_items_maintenances', methods:['GET'])]
    #[IsGranted(CompanyVoter::VIEW, 'company')]
    public function getAllClientMaintenances(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] ?Company $company,
        ): JsonResponse
    {
        $maintenances = $this->maintenanceService->getAllClientMaintenances($company);
        $serialzeData = $this->serializer->serialize($maintenances, 'json', ['groups' => 'get:light_maintenance']);
        return $this->format->sendSuccessSerializeResponse($serialzeData);
    }

    #[Route('/api/company/{companyUuid}/item/{itemUuid}/maintenances', name: 'get_all_client_items_maintenances', methods:['GET'])]
    #[IsGranted(CompanyVoter::VIEW, 'company')]
    public function getAllItemClientMaintenances(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] ?Company $company,
        #[MapEntity(mapping: ['itemUuid' => 'uuid'])] ?Item $item,
        ): JsonResponse
    {
        $maintenances = $this->maintenanceService->getAllItemClientMaitenances($company, $item);
        $serialzeData = $this->serializer->serialize($maintenances, 'json', ['groups' => 'get:light_maintenance']);
        return $this->format->sendSuccessSerializeResponse($serialzeData);
    }

    #[Route('/api/company/{companyUuid}/item/{itemUuid}/maintenance/{maintenanceUuid}', name: 'get_client_maintenance', methods:['GET'])]
    #[IsGranted(MaintenanceVoter::VIEW, 'maintenance')]
    public function getClientMaintenance(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] ?Company $company,
        #[MapEntity(mapping: ['itemUuid' => 'uuid'])] ?Item $item,
        #[MapEntity(mapping: ['maintenanceUuid' => 'uuid'])] ?Maintenance $maintenance,

        ): JsonResponse
    {
        $findMaintenance = $this->maintenanceService->getClientMaintenance($company, $item, $maintenance);
        $serialzeData = $this->serializer->serialize($findMaintenance, 'json', ['groups' => 'get:light_maintenance']);
        return $this->format->sendSuccessSerializeResponse($serialzeData);
    }

    #[Route('/api/company/{companyUuid}/item/{itemUuid}/maintenance', name: 'create_item_client_maintenance', methods:['POST'])]
    #[IsGranted(CompanyVoter::CREATE, 'company')]
    public function createClientMaintenance(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] ?Company $company,
        #[MapEntity(mapping: ['itemUuid' => 'uuid'])] ?item $item,
        Request $request
    ): JsonResponse
    {
        $payload = $request->getPayload()->all();

        try {
            $newMaintenance = $this->maintenanceService->createClientMaintenance($company, $item, $payload);
            $serialzeData = $this->serializer->serialize($newMaintenance, 'json', ['groups' => 'get:light_maintenance']);
            return $this->format->sendSuccessSerializeResponse($serialzeData);
        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }

    #[Route('/api/company/{companyUuid}/item/{itemUuid}/maintenance/{maintenanceUuid}', name: 'update_client_maintenance', methods:['PATCH'])]
    #[IsGranted(MaintenanceVoter::EDIT, 'maintenance')]
    public function updateClientMaintenance(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] ?Company $company,
        #[MapEntity(mapping: ['itemUuid' => 'uuid'])] ?Item $item,
        #[MapEntity(mapping: ['maintenanceUuid' => 'uuid'])] ?Maintenance $maintenance,
        Request $request
        ): JsonResponse
    {
        $payload = $request->getPayload()->all();

        try {
            $findMaintenance = $this->maintenanceService->updateClientMaintenance($company, $item, $maintenance, $payload);
            $serialzeData = $this->serializer->serialize($findMaintenance, 'json', ['groups' => 'get:light_maintenance']);
            return $this->format->sendSuccessSerializeResponse($serialzeData);
        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }

    #[Route('/api/company/{companyUuid}/item/{itemUuid}/maintenance/{maintenanceUuid}', name: 'delete_client_maintenance', methods:['DELETE'])]
    #[IsGranted(MaintenanceVoter::DELETE, 'maintenance')]
    public function deleteClientItemMaintenance(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] ?Company $company,
        #[MapEntity(mapping: ['itemUuid' => 'uuid'])] ?Item $item,
        #[MapEntity(mapping: ['maintenanceUuid' => 'uuid'])] ?Maintenance $maintenance,
        ): JsonResponse
    {
        try {
            $this->maintenanceService->deleteClientMaintenance($company, $item, $maintenance);
            return $this->format->sendSuccessReponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }

    #[Route('/api/company/{companyUuid}/item/{itemUuid}/maintenance/{maintenanceUuid}', name: 'delete_client_maintenance', methods:['DELETE'])]
    #[IsGranted(CompanyVoter::DELETE, 'company')]
    public function deleteClientItemMaintenances(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] ?Company $company,
        #[MapEntity(mapping: ['itemUuid' => 'uuid'])] ?Item $item,
        #[MapEntity(mapping: ['maintenanceUuid' => 'uuid'])] ?Maintenance $maintenance,
        Request $request
        ): JsonResponse
    {
        $payload = $request->getPayload()->all();

        try {
            $this->maintenanceService->updateClientMaintenance($company, $item, $maintenance, $payload);
            return $this->format->sendSuccessReponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }

}
