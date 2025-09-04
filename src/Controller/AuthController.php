<?php

namespace App\Controller;

use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class AuthController extends AbstractController
{

    public function __construct(
        private AuthService $authService
    ){}

    #[Route('/api/registration', name: 'app_auth_registration', methods:['POST'])]
    public function registration(Request $request): JsonResponse
    {
       $payload = $request->getPayload()->all();

        return $this->authService->registerNewUser($payload);

    }

    #[Route('/api/confirm-email/{token}', name: 'app_auth_confirm_email', methods:['GET'])]
    public function confirmEmail(string $token) :JsonResponse
    {
        return $this->authService->confirmEmail($token);
    }

    #[Route('/api/reset-password', name: 'app_auth_confirm_email', methods:['POST'])]
    public function resetPassword(Request $request)
    {
        $payload = $request->getPayload()->all();

        return $this->authService->resetPassword($payload);
    }

}
