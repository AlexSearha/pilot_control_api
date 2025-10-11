<?php

namespace App\Controller;

use App\Entity\Currency;
use App\Service\CurrencyService;
use App\Service\FormatService;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

final class CurrencyController extends AbstractController
{
    public function __construct(
        private CurrencyService $currencyService,
        private SerializerInterface $serializer,
        private FormatService $format
    ) {
    }

    // ---- Admin Routes ----

    #[Route('/api/currency', name: 'create_currency', methods:['POST'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function createCurrency(Request $request): JsonResponse
    {
        $payload = $request->getPayload()->all();

        try {
            $newCurrency = $this->currencyService->getAllCurrencies($payload);
            $serializeData = $this->serializer->serialize($newCurrency, 'json', ['groups' => 'get:light_currencies']);
            return $this->format->sendSuccessSerializeResponse($serializeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }

    }

    #[Route('/api/currency/{currencyUuid}', name: 'update_currency', methods:['PATCH'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function updateCurrency(
        #[MapEntity(mapping: ['currencyUuid' => 'uuid'])] ?Currency $currency,
        Request $request
    ): JsonResponse {
        $payload = $request->getPayload()->all();

        try {
            $updateCurrency = $this->currencyService->updateCurrency($currency, $payload);
            $serializeData = $this->serializer->serialize($updateCurrency, 'json', ['groups' => 'get:light_currencies']);
            return $this->format->sendSuccessSerializeResponse($serializeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }

    }

    #[Route('/api/currency/{currencyUuid}', name: 'delete_currency', methods:['DELETE'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function deleteCurrency(#[MapEntity(mapping: ['currencyUuid' => 'uuid'])] ?Currency $currency): JsonResponse
    {
        try {
            $this->currencyService->deleteCurrency($currency);
            return $this->format->sendSuccessReponse(null, Response::HTTP_NO_CONTENT);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }

    }

    // ---- Users Routes ----

    #[Route('/api/currencies', name: 'get_all_currencies', methods:['GET'])]
    #[IsGranted('ROLE_EMPLOYEE')]
    public function getAllCurrencies(): JsonResponse
    {
        $allCurrencies = $this->currencyService->getAllCurrencies();

        $serializeData = $this->serializer->serialize($allCurrencies, 'json', ['groups' => 'get:light_currencies']);

        return $this->format->sendSuccessSerializeResponse($serializeData);

    }

    #[Route('/api/currency/{currencyUuid}', name: 'get_one_currency', methods:['GET'])]
    #[IsGranted('ROLE_EMPLOYEE')]
    public function getOneCurrency(#[MapEntity(mapping: ['currencyUuid' => 'uuid'])] ?Currency $currency): JsonResponse
    {
        $currency = $this->currencyService->getOneCurrency($currency);

        $serializeData = $this->serializer->serialize($currency, 'json', ['groups' => 'get:light_currencies']);

        return $this->format->sendSuccessSerializeResponse($serializeData);

    }
}
