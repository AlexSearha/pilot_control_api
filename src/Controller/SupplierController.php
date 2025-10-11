<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Supplier;
use App\Security\Voter\CompanyVoter;
use App\Security\Voter\SupplierVoter;
use App\Service\FormatService;
use App\Service\SupplierServices;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

final class SupplierController extends AbstractController
{
    public function __construct(
        private SupplierServices $supplierServices,
        private SerializerInterface $serializer,
        private FormatService $format
    ) {
    }
    // ---- Admin Routes

    #[Route('/api/suppliers', name: 'app_get_all_suppliers', methods:['GET'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function getAllSuppliers(): JsonResponse
    {
        $suppliers = $this->supplierServices->getAllSuppliers();
        $serializeData = $this->serializer->serialize($suppliers, 'json', ['groups' => 'get:light:supplier']);
        return $this->format->sendSuccessSerializeResponse($serializeData);
    }

    // ---- User Routes

    #[Route('/api/company/{companyUuid}/suppliers', name: 'app_get_all_client_suppliers', methods:['GET'])]
    #[IsGranted(CompanyVoter::VIEW, 'company')]
    public function getAllClientSuppliers(#[MapEntity(mapping: ['companyUuid' => 'uuid'])] Company $company): JsonResponse
    {
        try {
            $suppliers = $this->supplierServices->getClientSuppliers($company);
            $serializeData = $this->serializer->serialize($suppliers, 'json', ['groups' => 'get:light:supplier']);
            return $this->format->sendSuccessSerializeResponse($serializeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());

        }
    }

    #[Route('/api/company/{companyUuid}/supplier/{supplierUuid}', name: 'app_get_client_supplier', methods:['GET'])]
    #[IsGranted(SupplierVoter::VIEW, 'supplier')]
    public function getOneClientSupplier(#[MapEntity(mapping: ['companyUuid' => 'uuid'])] Company $company, #[MapEntity(mapping: ['supplierUuid' => 'uuid'])] Supplier $supplier): JsonResponse
    {
        try {
            $this->supplierServices->getOneSupplier($company, $supplier);
            $serializeData = $this->serializer->serialize($supplier, 'json', ['groups' => 'get:light:supplier']);
            return $this->format->sendSuccessSerializeResponse($serializeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());

        }
    }

    #[Route('/api/company/{companyUuid}/supplier', name: 'app_create_client_supplier', methods:['POST'])]
    #[IsGranted(CompanyVoter::VIEW, 'supplier')]
    public function createClientSupplier(#[MapEntity(mapping: ['companyUuid' => 'uuid'])] Company $company, Request $request): JsonResponse
    {
        $payload = $request->getPayload()->all();

        try {
            $newSupplier = $this->supplierServices->createSupplier($company, $payload);
            $serializeData = $this->serializer->serialize($newSupplier, 'json', ['groups' => 'get:light:supplier']);
            return $this->format->sendSuccessSerializeResponse($serializeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());

        }
    }

    #[Route('/api/company/{companyUuid}/supplier/{supplierUuid}', name: 'app_update_client_supplier', methods:['PATCH'])]
    #[IsGranted(SupplierVoter::EDIT, 'supplier')]
    public function updateClientSupplier(#[MapEntity(mapping: ['companyUuid' => 'uuid'])] Company $company, #[MapEntity(mapping: ['supplierUuid' => 'uuid'])] Supplier $supplier, Request $request): JsonResponse
    {
        $payload = $request->getPayload()->all();

        try {
            $updateSupplier = $this->supplierServices->updateOneSupplier($company, $supplier, $payload);
            $serializeData = $this->serializer->serialize($updateSupplier, 'json', ['groups' => 'get:light:supplier']);
            return $this->format->sendSuccessSerializeResponse($serializeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());

        }
    }

    #[Route('/api/company/{companyUuid}/supplier/{supplierUuid}', name: 'app_delete_client_supplier', methods:['DELETE'])]
    #[IsGranted(SupplierVoter::DELETE, 'supplier')]
    public function deleteClientSupplier(#[MapEntity(mapping: ['companyUuid' => 'uuid'])] Company $company, #[MapEntity(mapping: ['supplierUuid' => 'uuid'])] Supplier $supplier): JsonResponse
    {
        try {
            $this->supplierServices->deleteSupplier($company, $supplier);
            return $this->format->sendSuccessReponse(null, Response::HTTP_NO_CONTENT);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());

        }
    }
}
