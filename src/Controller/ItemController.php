<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Item;
use App\Security\Voter\CompanyVoter;
use App\Security\Voter\ItemVoter;
use App\Service\FormatService;
use App\Service\ItemService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

final class ItemController extends AbstractController
{
    public function __construct(
        private ItemService $itemService,
        private SerializerInterface $serializer,
        private FormatService $format
    )
    {}

    // ---- Super Admin Routes ----

    #[Route('/api/items', name: 'item_get_all', methods:['GET'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function getAllItems(): JsonResponse
    {
        $items = $this->itemService->getAllItems();

        $serialzeData = $this->serializer->serialize($items, 'json', ['groups' => 'get:light_item']);

        return $this->format->sendSuccessSerializeResponse($serialzeData);
    }

    // ---- Users Routes ----

    #[Route('/api/company/{companyUuid}/items', name: 'item_get_all_client_items', methods:['GET'])]
    #[IsGranted(CompanyVoter::VIEW, 'company')]
    public function getClientAllItems(#[MapEntity(mapping: ['companyUuid' => 'uuid'])] Company $company): JsonResponse
    {
        try {

            $items = $this->itemService->getClientAllItems($company);

            $serialzeData = $this->serializer->serialize($items, 'json', ['groups' => 'get:light_item']);

            return $this->format->sendSuccessSerializeResponse($serialzeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }

    #[Route('/api/company/{companyUuid}/item/{itemUuid}', name: 'item_get_client_item', methods:['GET'])]
    #[IsGranted(ItemVoter::VIEW, 'item')]
    public function getClienItem(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] Company $company,
        #[MapEntity(mapping: ['itemUuid' => 'uuid'])] Item $item
    ): JsonResponse
    {
        try {

            $items = $this->itemService->getClientItem($company, $item);
            $serialzeData = $this->serializer->serialize($items, 'json', ['groups' => 'get:light_item']);

            return $this->format->sendSuccessSerializeResponse($serialzeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }

    #[Route('/api/company/{companyUuid}/item', name: 'item_create_client_item', methods:['POST'])]
    #[IsGranted(CompanyVoter::CREATE, 'company')]
    public function createClienItem(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] Company $company,
        Request $request
    ): JsonResponse
    {
        $payload = $request->getPayload()->all();

        try {

            $items = $this->itemService->createClientItem($company, $payload);
            $serialzeData = $this->serializer->serialize($items, 'json', ['groups' => 'get:light_item']);

            return $this->format->sendSuccessSerializeResponse($serialzeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }

    #[Route('/api/company/{companyUuid}/item/{itemUuid}', name: 'item_update_client_item', methods:['PATCH'])]
    #[IsGranted(ItemVoter::EDIT, 'item')]
    public function updateClienItem(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] Company $company,
        #[MapEntity(mapping: ['itemUuid' => 'uuid'])] Item $item,
        Request $request
    ): JsonResponse
    {
        $payload = $request->getPayload()->all();

        try {

            $items = $this->itemService->updateClientItem($company, $item, $payload);
            $serialzeData = $this->serializer->serialize($items, 'json', ['groups' => 'get:light_item']);
            return $this->format->sendSuccessSerializeResponse($serialzeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }

    #[Route('/api/company/{companyUuid}/item/{itemUuid}', name: 'item_delete_client_item', methods:['DELETE'])]
    #[IsGranted(ItemVoter::DELETE, 'item')]
    public function deleteClienItem(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] Company $company,
        #[MapEntity(mapping: ['itemUuid' => 'uuid'])] Item $item,
    ): JsonResponse
    {
        try {

            $this->itemService->deleteItem($company, $item);
            return $this->format->sendSuccessReponse(null, Response::HTTP_NO_CONTENT);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }

    #[Route('/api/company/{companyUuid}/items', name: 'item_delete_client_items', methods:['DELETE'])]
    #[IsGranted(CompanyVoter::DELETE, 'company')]
    public function deleteClienItems(
        #[MapEntity(mapping: ['companyUuid' => 'uuid'])] Company $company,
        Request $request
    ): JsonResponse
    {
        $payload = $request->getPayload()->all();

        try {

            $this->itemService->deleteItems($company, $payload);
            return $this->format->sendSuccessReponse(null, Response::HTTP_NO_CONTENT);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }

}
