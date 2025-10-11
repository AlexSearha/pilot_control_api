<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Supplier;
use App\Entity\SupplierOrder;
use App\Security\Voter\CompanyVoter;
use App\Security\Voter\SupplierOrderVoter;
use App\Security\Voter\SupplierVoter;
use App\Service\FormatService;
use App\Service\SupplierOrderService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

final class SupplierOrderController extends AbstractController
{
    public function __construct(
        private SerializerInterface $serializer,
        private FormatService $format,
        private SupplierOrderService $supplierOrderService
    ) {
    }

    // ---- Admin Routes

    #[Route('/api/supplier-orders', name: 'app_get_all_supplier_orders', methods:['GET'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function getAllSupplierOrders(): JsonResponse
    {
        $supplierOrder = $this->supplierOrderService->getAllSupplierOrders();
        $serializeData = $this->serializer->serialize($supplierOrder, 'json', ['groups' => 'get:light:supplier-order']);
        return $this->format->sendSuccessSerializeResponse($serializeData);
    }

    // ---- User Routes

    #[Route('/api/company/{companyUuid}/supplier/{supplierUuid}/orders', name: 'app_get_all_client_suppliers', methods:['GET'])]
    #[IsGranted(SupplierVoter::VIEW, 'supplier')]
    public function getAllClientSupplierOrders(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] Company $company,
        #[MapEntity(mapping: ['supplierUuid' => 'uuid'])] Supplier $supplier
    ): JsonResponse {
        try {
            $suppliers = $this->supplierOrderService->getAllClientSupplierOrders($company, $supplier);
            $serializeData = $this->serializer->serialize($suppliers, 'json', ['groups' => 'get:light:supplier-order']);
            return $this->format->sendSuccessSerializeResponse($serializeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());

        }
    }

    #[Route('/api/company/{companyUuid}/supplier/{supplierUuid}/order/{orderUuid}', name: 'app_get_client_supplier_order', methods:['GET'])]
    #[IsGranted(SupplierOrderVoter::VIEW, 'supplierOrder')]
    public function getClientSupplierOrder(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] Company $company,
        #[MapEntity(mapping: ['supplierUuid' => 'uuid'])] Supplier $supplier,
        #[MapEntity(mapping: ['orderUuid' => 'uuid'])] SupplierOrder $supplierOrder
    ): JsonResponse {
        try {
            $suppliers = $this->supplierOrderService->getClientSupplierOrder($company, $supplier, $supplierOrder);
            $serializeData = $this->serializer->serialize($suppliers, 'json', ['groups' => 'get:light:supplier-order']);
            return $this->format->sendSuccessSerializeResponse($serializeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());

        }
    }

    #[Route('/api/company/{companyUuid}/supplier/{supplierUuid}/order', name: 'app_create_client_supplier_order', methods:['POST'])]
    #[IsGranted(CompanyVoter::VIEW, 'company')]
    public function createClientSupplierOrder(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] Company $company,
        #[MapEntity(mapping: ['supplierUuid' => 'uuid'])] Supplier $supplier,
        Request $request
    ): JsonResponse {
        $payload = $request->getPayload()->all();

        try {
            $suppliers = $this->supplierOrderService->createSupplierOrder($company, $supplier, $payload);
            $serializeData = $this->serializer->serialize($suppliers, 'json', ['groups' => 'get:light:supplier-order']);
            return $this->format->sendSuccessSerializeResponse($serializeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());

        }
    }

    #[Route('/api/company/{companyUuid}/supplier/{supplierUuid}/order/{orderUuid}', name: 'app_delete_client_supplier_order', methods:['DELETE'])]
    #[IsGranted(SupplierOrderVoter::DELETE, 'supplierOrder')]
    public function deleteClientSupplierOrder(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] Company $company,
        #[MapEntity(mapping: ['supplierUuid' => 'uuid'])] Supplier $supplier,
        #[MapEntity(mapping: ['orderUuid' => 'uuid'])] SupplierOrder $supplierOrder
    ): JsonResponse {
        try {
            $this->supplierOrderService->deleteClientSupplierOrder($company, $supplier, $supplierOrder);
            return $this->format->sendSuccessReponse(null, Response::HTTP_NO_CONTENT);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());

        }
    }

    #[Route('/api/company/{companyUuid}/supplier/{supplierUuid}/orders', name: 'app_delete_client_supplier_orders', methods:['DELETE'])]
    #[IsGranted(SupplierOrderVoter::DELETE, 'supplierOrder')]
    public function deleteClientSupplierOrders(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] Company $company,
        #[MapEntity(mapping: ['supplierUuid' => 'uuid'])] Supplier $supplier,
        Request $request
    ): JsonResponse {
        $payload = $request->getPayload()->all();

        try {
            $this->supplierOrderService->deleteClientSupplierOrders($company, $supplier, $payload);
            return $this->format->sendSuccessReponse(null, Response::HTTP_NO_CONTENT);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());

        }
    }

}
