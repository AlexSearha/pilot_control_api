<?php

namespace App\Controller;

use App\Security\Voter\AccessVoter;
use App\Service\FormatService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UserController extends AbstractController
{

    public function __construct(
        private UserService $userService,
        private FormatService $format,
        private SerializerInterface $serializer,
    )
    {}

    // ---- Admins Routes ----
    #[Route('/api/users', name: 'app_user_all_users', methods:['GET'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function getAllUsers(): JsonResponse
    {
        $allUsers = $this->userService->getAllUser();

        $serializeData = $this->serializer->serialize($allUsers, 'json', ['groups' => 'get:auth_me']);

        return $this->format->sendSuccessSerializeResponse($serializeData);

    }

    #[Route('/api/user/{uuid}', name: 'app_user_one_user', methods:['GET'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function getOneUser(string $uuid): JsonResponse
    {
        try {

            $allUsers = $this->userService->getOneUser($uuid);
            $serializeData = $this->serializer->serialize($allUsers, 'json', ['groups' => 'get:auth_me']);

            return $this->format->sendSuccessSerializeResponse($serializeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }

    #[Route('/api/user', name: 'app_user_create_user', methods:['POST'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function createUser(Request $request): JsonResponse
    {

        $payload = $request->getPayload()->all();

        try {
            $newUser = $this->userService->createUser($payload);
            $serializeData= $this->serializer->serialize($newUser, 'json', ['groups' => 'get:auth_me']);

            return $this->format->sendSuccessSerializeResponse($serializeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }

    #[Route('/api/user/{userUuid}', name: 'app_user_update_user', methods:['PATCH'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function updateUser(Request $request, string $userUuid): JsonResponse
    {

        $payload = $request->getPayload()->all();

        try {
            $user = $this->userService->updateUser($payload, $userUuid);
            $serializeData= $this->serializer->serialize($user, 'json', ['groups' => 'get:auth_me']);

            return $this->format->sendSuccessSerializeResponse($serializeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }

    #[Route('/api/user/{userUuid}', name: 'app_user_delete_user', methods:['DELETE'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function deleteUser(string $userUuid): JsonResponse
    {

        try {
            $this->userService->deleteUser($userUuid);

            return $this->format->sendSuccessReponse(null, Response::HTTP_NO_CONTENT);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }

    #[Route('/api/users', name: 'app_user_delete_users', methods:['DELETE'])]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function deleteUsers(Request $request): JsonResponse
    {
        $payload = $request->getPayload()->all();

        try {
            $this->userService->deleteUsers($payload);

            return $this->format->sendSuccessReponse(null, Response::HTTP_NO_CONTENT);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());
        }
    }

    #[Route('/api/company/{companyUuid}/users', name: 'app_company_get_all_users', methods:['GET'])]
    public function getClientAllUsers(string $companyUuid): JsonResponse
    {
        try {
            $allUsers = $this->userService->getClientAllUsers($companyUuid);
            $serializeData = $this->serializer->serialize($allUsers, 'json', ['groups' => 'get:auth_me']);
            return $this->format->sendSuccessSerializeResponse($serializeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());

        }
    }

    #[Route('/api/company/{companyUuid}/users/{userUuid}', name: 'app_company_get_one_user', methods:['GET'])]
    public function getClientOneUser(string $companyUuid, string $userUuid): JsonResponse
    {
        try {
            $user = $this->userService->getClientOneUser($companyUuid, $userUuid);
            $serializeData = $this->serializer->serialize($user, 'json', ['groups' => 'get:auth_me']);
            return $this->format->sendSuccessSerializeResponse($serializeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());

        }
    }

    #[Route('/api/company/{companyUuid}/users', name: 'app_company_create_user', methods:['POST'])]
    public function createClientUser(Request $request, string $companyUuid): JsonResponse
    {
        $payload = $request->getPayload()->all();

        try {
            $user = $this->userService->createUser($payload, $companyUuid);
            $serializeData = $this->serializer->serialize($user, 'json', ['groups' => 'get:auth_me']);
            return $this->format->sendSuccessSerializeResponse($serializeData);

        } catch (\Exception $e) {
            return $this->format->sendErrorReponse($e->getMessage(), $e->getCode());

        }
    }
}
