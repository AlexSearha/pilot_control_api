<?php


namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthService extends AbstractController
{
    public function __construct(
        private ValidatorInterface $validator,
        private UserPasswordHasherInterface $passwordHasher,
        private FormatService $formatService,
        private EntityManagerInterface $em,
        private UserRepository $userRepo,
        private MailerService $mailerService,
        private JWTTokenManagerInterface $JWTManager,
    )
    {}

    private function generateEmailConfirmationToken(User $user): string
    {
        $payload = [
            'email' => $user->getEmail(),
            'purpose' => 'email_confirmation',
            'exp' => (new \DateTimeImmutable('+5 minute'))->getTimestamp(),
        ];

        return $this->JWTManager->createFromPayload($user, $payload);
    }

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
            return $this->formatService->sendErrorJsonReponse("Un compte avec cette adresse e-mail existe déjà.", Response::HTTP_BAD_REQUEST);
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

            return $this->formatService->sendErrorJsonReponse($errorMessages, Response::HTTP_FORBIDDEN);
        }

        $this->em->persist($newUser);
        $this->em->flush();


        $token = $this->generateEmailConfirmationToken($newUser);
        $this->mailerService->sendSimpleEmail($token);

        return $this->formatService->sendSuccessJsonReponse([
            'uuid'      => $newUser->getUuid(),
            'email'     => $newUser->getEmail(),
            'firstname' => $newUser->getFirstname(),
            'lastname'  => $newUser->getLastname(),
            'phone'     => $newUser->getPhone(),
            'username'  => $newUser->getUsername(),
        ], Response::HTTP_CREATED);
    }

    public function confirmEmail(string $token) : JsonResponse
    {
        try {
            $decodeToken = $this->JWTManager->parse($token);
        } catch (\Exception $e) {
            return $this->formatService->sendErrorJsonReponse('Votre jeton est invalide ou a expiré', Response::HTTP_BAD_REQUEST);
        }

        if ($decodeToken['exp'] < (new DateTimeImmutable())->getTimestamp()) {
            return $this->formatService->sendErrorJsonReponse('Votre jeton est invalide ou a expiré', Response::HTTP_BAD_REQUEST);
        }

        $email = $decodeToken['email'];
        $user = $this->userRepo->findOneBy(['email' => $email]);

        if (!$user) {
            return $this->formatService->sendErrorJsonReponse("L'utilisateur n'existe pas", Response::HTTP_NOT_FOUND);
        }

        $user->setUserConfirmed(true);
        try {
            $this->em->flush();
        } catch (\Exception $e) {
            return $this->formatService->sendErrorJsonReponse("Une erreur s'est produite");
        }

        return $this->formatService->sendSuccessJsonReponse(['message' => 'Votre compte est confirmé']);
    }

    public function resetpassword(array $payload)
    {
        $email          = $payload['email'] ?? null;
        $newPassword    = $payload['password'] ?? null;

        $user = $this->userRepo->findOneBy(['email' => $email]);
        if (!$user) {
            return $this->formatService->sendErrorJsonReponse("L'utilisateur n'existe pas", Response::HTTP_NOT_FOUND);
        }

        $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword));

        try {
            $this->em->flush();
        } catch (\Exception $e) {
            return $this->formatService->sendErrorJsonReponse("Une erreur s'est produite");
        }

        return $this->formatService->sendSuccessJsonReponse(['message' => 'Votre mot de passe à été modifié avec succès']);
    }
}
