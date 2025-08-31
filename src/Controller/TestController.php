<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class TestController extends AbstractController
{
    #[Route('/api/test', name: 'app_test')]
    public function test(): JsonResponse
    {
        return $this->json([
            'message' => 'Caa ma tellement pris la tete !  ohlalaaaaaaaaaaaaaaaaaaaa',
        ]);
    }
}
