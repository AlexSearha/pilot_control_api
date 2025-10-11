<?php

namespace App\Controller;

use App\Service\FormatService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class CurrencyController extends AbstractController
{
    public function __construct(
        private SerializerInterface $serializer,
        private FormatService $format
    ) {
    }

    #[Route('/api/currency', name: 'app_currency')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/CurrencyController.php',
        ]);
    }
}
