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
use Symfony\Component\Security\Core\User\UserInterface;
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
        private LoggerInterface $logger,
    )
    {}

    /**
     * Registers a new user in the database.
     *
     * - Checks if the email is already in use.
     * - Validates the User entity constraints.
     * - Hashes the password and persists the user.
     * - Generates a confirmation token and sends a confirmation email.
     *
     * @param array<string, mixed> $payload  New user data,
     *                                       expected keys: ['email' => string, 'password' => string]
     *
     * @return JsonResponse
     *
     * @throws \Exception If an unexpected error occurs during persistence
     */
    public function registerNewUser(mixed $payload) : JsonResponse
    {
        $email = $payload['email'] ?? null;
        $password = $payload['password'] ?? null;
        $errorMessages = [];

        $findUser = $this->userRepo->findOneBy(['email' => $email]);
        if ($findUser) {
            return $this->formatService->sendErrorReponse("Un compte avec cette adresse e-mail existe déjà.", Response::HTTP_BAD_REQUEST);
        }

        $newUser = new User();
        $newUser
            ->setEmail($email)
            ->setPassword($this->passwordHasher->hashPassword($newUser, $password));

        $errors = $this->validator->validate($newUser);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return $this->formatService->sendErrorReponse($errorMessages, Response::HTTP_FORBIDDEN);
        }

        $this->em->persist($newUser);
        $this->em->flush();

        $token = $this->tokenService->generateExpiredToken($newUser, 'email_confirmation');
        $this->mailerService->sendSimpleEmail($token, $newUser, 'Confirmation email');

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     * Confirms a user's email address based on a given token.
     *
     * - Parses and validates the token.
     * - Checks if the token has expired.
     * - Finds the user associated with the email in the token.
     * - Marks the user as confirmed if found.
     *
     * @param string $token  The confirmation token received by the user
     *
     * @return JsonResponse
     *
     * @throws \Exception If an unexpected error occurs during database operations
     */
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

    /**
     * Initiates the password reset process for a user.
     *
     * - Looks up the user by email.
     * - Generates a short-lived reset password token.
     * - Sends the reset password email to the user.
     *
     * @param array<string, mixed> $payload  Data containing the user's email
     *
     * @return JsonResponse|null Returns a success response if the user exists,
     *                           or null if the user is not found
     *
     * @throws \Exception If an unexpected error occurs during token generation or email sending
     */
    public function resetpassword(array $payload)
    {
        $email = $payload['email'] ?? null;

        $user = $this->userRepo->findOneBy(['email' => $email]);
        if (!$user) {
            return;
        }

        $generateToken = $this->tokenService->generateExpiredToken($user, 'reset_password');

        $this->mailerService->sendSimpleEmail($generateToken, $user, 'Reset password');

        return $this->formatService->sendSuccessReponse();
    }

    /**
     * Completes the password reset process for a user.
     *
     * - Parses and validates the reset password token.
     * - Ensures the token purpose is valid and not expired.
     * - Finds the user associated with the token.
     * - Updates the user's password with the newly provided one.
     *
     * @param array<string, mixed> $payload  Data containing the reset token and the new password
     *
     * @return JsonResponse|null Returns a success or error response depending on the outcome,
     *                           or null if the user is not found
     *
     * @throws \Exception If an unexpected error occurs during the database update
     */
    public function resetPasswordCheck(array $payload)
    {

        $token      = $payload['token'] ?? null;
        $password   = $payload['password'] ?? null;

        $decodeToken = $this->tokenService->tokenParser($token);

        if ($decodeToken['purpose'] !== 'reset_password') {
            return $this->formatService->sendErrorReponse('Une erreur est survenue');
        }

        if (!$this->tokenService->checkTokenValidity($token)) {
            return $this->formatService->sendErrorReponse('Le jeton est expiré/invalide', Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->userRepo->findOneBy(['email' => $decodeToken['email']]);
        if (!$user) {
            $this->formatService->sendErrorReponse('Utilisateur non trouvé', Response::HTTP_NOT_FOUND);
        }

        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        try {
            $this->em->flush();
            return $this->formatService->sendSuccessReponse();
        } catch (\Exception $e) {
            $this->formatService->sendErrorReponse('une erreur est survenue');

        }
    }

    /**
     * Changes the authenticated user's password.
     *
     * - Validates that all required password fields are provided.
     * - Ensures the current (old) password is correct.
     * - Updates the user's password with the new one.
     *
     * @param array<string, mixed> $payload  Data containing:
     *                                       - oldPassword: string
     *                                       - newPassword: string
     *                                       - checkNewPassword: string
     *
     * @return JsonResponse Returns a success or error response depending on the outcome
     *
     * @throws \Exception If an unexpected error occurs during the database update
     */
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

    /**
     * Retrieves information about a specific user.
     *
     * - Serializes the user data with the 'get:auth_me' serialization group.
     * - Returns a success response with the serialized data.
     *
     * @param User $user  The user entity whose information is being retrieved
     *
     * @return JsonResponse Returns a success response with user data,
     *                      or an error response if the user is invalid
     */
    public function getUserInformation(User $user) : JsonResponse
    {
        if (!$user) return $this->formatService->sendErrorReponse("Une erreur s'est produite");

        $serializeData = $this->serializer->serialize($user, 'json', ['groups' => 'get:auth_me']);

        return $this->formatService->sendSuccessSerializeResponse($serializeData);
    }

    /**
     * Generates a unique random numeric string of the specified length.
     *
     * This method repeatedly generates a numeric string of the given length
     * until it finds one that is not already assigned as an auth code
     * to any existing user in the repository.
     *
     * @param int $digits The number of digits for the generated code. Defaults to 6.
     *
     * @return string A unique numeric string of the specified length.
     */
    public function generateRandomDigits(int $digits = 6): string
    {
        do {
            $number = $this->generateDigits($digits);

            $existingUser = $this->userRepo->findOneBy(['authCode' => $number]);

        } while ($existingUser !== null);

        return $number;
    }

    /**
     * Generates a random numeric string with the specified number of digits.
     *
     * The generated string will always have exactly the given length,
     * padding with leading zeros if necessary.
     *
     * @param int $digits The number of digits to generate.
     *
     * @return string A numeric string of the specified length.
     *
     */
    private function generateDigits(int $digits): string
    {
        $max = (10 ** $digits) - 1;
        $generateInt = random_int(0, $max);

        return str_pad((string) $generateInt, $digits, '0', STR_PAD_LEFT);
    }

}
