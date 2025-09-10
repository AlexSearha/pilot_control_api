<?php

namespace App\Controller;

use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

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

    #[Route('/api/confirm-email', name: 'app_auth_confirm-email', methods:['POST'])]
    public function confirmEmail(Request $request) :JsonResponse
    {
       $payload = $request->getPayload()->all();

        return $this->authService->confirmEmail($payload['token']);
    }

    #[Route('/api/reset/password-check', name: 'app_auth_reset_password_check', methods:['POST'])]
    public function resetPasswordCheck(Request $request): JsonResponse
    {
        $payload = $request->getPayload()->all();

        return $this->authService->resetPasswordCheck($payload);
    }

    #[Route('/api/reset/password', name: 'app_auth_reset_password', methods:['POST'])]
    public function resetPassword(Request $request): JsonResponse
    {
        $payload = $request->getPayload()->all();

        return $this->authService->resetPassword($payload);
    }

    #[Route('/api/change-password', name: 'app_auth_change_password', methods:['POST'])]
    public function changePassword(Request $request): JsonResponse
    {
        $payload = $request->getPayload()->all();

        return $this->authService->changePassword($payload);
    }

    #[Route('/api/me', name: 'app_auth_me', methods:['GET'])]
    public function userInformations(#[CurrentUser] $user): JsonResponse
    {
        return $this->authService->getUserInformation($user);
    }

}
