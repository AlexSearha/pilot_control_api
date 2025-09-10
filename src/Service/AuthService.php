<?php


namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthService extends AbstractController
{
    public function __construct(
        private ValidatorInterface $validator,
        private UserPasswordHasherInterface $passwordHasher,
        private FormatService $formatService,
        private TokenService $tokenService,
        private EntityManagerInterface $em,
        private UserRepository $userRepo,
        private MailerService $mailerService,
        private SerializerInterface $serializer,
        private LoggerInterface $logger
    )
    {}


    public function registerNewUser(mixed $payload) : JsonResponse
    {
        $email = $payload['email'] ?? null;
        $password = $payload['password'] ?? null;
        $firstname = $payload["firstname"] ?? null;
        $lastname = $payload["lastname"] ?? null;
        $phone = $payload["phone"] ?? null;
        $username = $payload["username"] ?? null;
        $errorMessages = [];

        $findUser = $this->userRepo->findOneBy(['email' => $email]);

        if ($findUser) {
            return $this->formatService->sendErrorReponse("Un compte avec cette adresse e-mail existe déjà.", Response::HTTP_BAD_REQUEST);
        }

        $newUser = new User();

        $newUser
            ->setEmail($email)
            ->setPassword($this->passwordHasher->hashPassword($newUser, $password))
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setPhone($phone);


        if (!$username && $firstname !== null && $lastname !== null) {
            $newUser->setUsername($firstname . '.' . $lastname);
        }


        $errors = $this->validator->validate($newUser);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return $this->formatService->sendErrorReponse($errorMessages, Response::HTTP_FORBIDDEN);
        }

        $this->em->persist($newUser);
        $this->em->flush();


        $token = $this->tokenService->generateTokenFromPayload($newUser, 'email_confirmation');
        $this->mailerService->sendSimpleEmail($token, $newUser, 'Confirmation email');

        return $this->json(null, Response::HTTP_CREATED);
    }

    public function confirmEmail(string $token) : JsonResponse
    {
        try {
            $decodeToken = $this->tokenService->tokenParser($token);
        } catch (\Exception $e) {
            return $this->formatService->sendErrorReponse('Votre jeton est invalide ou a expiré', Response::HTTP_BAD_REQUEST);
        }

        if ($decodeToken['exp'] < (new DateTimeImmutable())->getTimestamp()) {
            return $this->formatService->sendErrorReponse('Votre jeton est invalide ou a expiré', Response::HTTP_BAD_REQUEST);
        }

        $email = $decodeToken['email'];
        $user = $this->userRepo->findOneBy(['email' => $email]);

        if (!$user) {
            return $this->formatService->sendErrorReponse("L'utilisateur n'existe pas", Response::HTTP_NOT_FOUND);
        }

        $user->setUserConfirmed(true);
        try {
            $this->em->flush();
            return $this->formatService->sendSuccessReponse(['message' => 'Votre compte est confirmé']);
        } catch (\Exception $e) {
            return $this->formatService->sendErrorReponse("Une erreur s'est produite", $e->getCode());
        }

    }

    public function resetpassword(array $payload)
    {
        $email = $payload['email'] ?? null;

        $user = $this->userRepo->findOneBy(['email' => $email]);
        if (!$user) {
            return;
        }

        $generateToken = $this->tokenService->generateTokenFromPayload($user, 'reset_password', '+30 minutes');

        $this->mailerService->sendSimpleEmail($generateToken, $user, 'Reset password');

        return $this->formatService->sendSuccessReponse();
    }

    public function resetPasswordCheck(array $payload)
    {
        $token = $payload['token'] ?? null;

        $decodeToken = $this->tokenService->tokenParser($token);

        dd($decodeToken);
    }

    public function changePassword(array $payload)
    {
        /** @var User $userSession */
        $userSession        = $this->getUser();
        $oldPassword        = $payload['oldPassword']?? null;
        $newPassword        = $payload['newPassword'] ?? null;
        $checkNewPassword   = $payload['checkNewPassword'] ?? null;

        if (!$oldPassword || !$newPassword || !$checkNewPassword) {
            return $this->formatService->sendErrorReponse("Le mot de passe ne respecte pas les critères de sécurité requis.", Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->userRepo->findOneBy(['email' => $userSession->getEmail()]);
        if (!$user) {
            return $this->formatService->sendErrorReponse("L'utilisateur n'existe pas", Response::HTTP_NOT_FOUND);
        }

        $isOldPasswordValid = $this->passwordHasher->isPasswordValid($user, $oldPassword);
        if (!$isOldPasswordValid) {
            return $this->formatService->sendErrorReponse("Mot de passe incorrect", Response::HTTP_UNAUTHORIZED);
        }

        $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword));

        try {
            $this->em->flush();
            return $this->formatService->sendSuccessReponse();
        } catch (\Exception $e) {
            return $this->formatService->sendErrorReponse("Une erreur s'est produite", Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getUserInformation(User $user) : JsonResponse
    {
        if (!$user) return $this->formatService->sendErrorReponse("Une erreur s'est produite");

        $serializeData = $this->serializer->serialize($user, 'json', ['groups' => 'get:auth_me']);

        return $this->formatService->sendSuccessSerializeResponse($serializeData);
    }
}
