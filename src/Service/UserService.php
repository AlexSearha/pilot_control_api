<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\CompanyRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserService extends AbstractController
{
    private $errorsStringify = [];

    public function __construct(
        private UserRepository $userRepo,
        private ValidatorInterface $validator,
        private UserPasswordHasherInterface $passwordHasher,
        private FormatService $format,
        private EntityManagerInterface $em,
        private CompanyRepository $companyRepo,
        private CompanyService $companyService
    )
    {}

    // ---- Admin Functions ----
    public function getAllUser()
    {
        return $this->userRepo->findBy([],['email' => 'ASC']);
    }

    public function getOneUser(string $userUuid): User
    {
        $user = $this->userRepo->findOneBy(['uuid' => $userUuid]);
        if (!$user) {
            throw new \Exception("Utilisateur inconnu", Response::HTTP_NOT_FOUND);
        }

        return $user;
    }

    public function createUser(array $payload, $compagnyUuid = null)
    {
        if (count($payload) === 0) {
            throw new \Exception("Aucune données de reçues", Response::HTTP_NOT_FOUND);
        }

        if (!isset($payload['email']) || !isset($payload['password']) || (!$compagnyUuid && !isset($payload['companyUuid']))) {
            throw new \Exception("Email / Mot de passe / Société est manquant", Response::HTTP_BAD_REQUEST);
        }

        $findUser = $this->userRepo->findOneBy(['email' => $payload['email']]);
        if ($findUser) {
            throw new \Exception("L'utilisateur existe déjà", Response::HTTP_BAD_REQUEST);

        }

        $newUser = new User();
        $newUser
            ->setEmail($payload['email'])
            ->setPassword($this->passwordHasher->hashPassword($newUser, $payload['password']));

        $compagny = $this->companyService->getOneCompany($compagnyUuid ?? $payload['companyUuid']);

        $newUser->setCompany($compagny);

        if (isset($payload['lastname'])) {
            $newUser->setLastname($payload['lastname']);
        }

        if (isset($payload['firstname'])) {
            $newUser->setFirstname($payload['firstname']);
        }

        if (isset($payload['phone'])) {
            $newUser->setPhone($payload['phone']);
        }

        if (isset($payload['jobTitle'])) {
            $newUser->setJobTitle($payload['jobTitle']);
        }

        if (isset($payload['roles'])) {
            $newUser->setRoles($payload['roles']);
        }

        $errors = $this->validator->validate($newUser);

        if (count($errors) > 0) {

            foreach ($errors as $error) {
                $this->errorsStringify[] = $error->getMessage();
            }
            throw new \Exception(implode(',', $this->errorsStringify), Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->em->persist($newUser);
            $this->em->flush();
            $this->em->refresh($newUser);

            return $newUser;

        } catch (\Exception $e) {
            throw new \Exception("une erreur s'est produite", $e->getCode());
        }
    }

    public function updateUser(array $payload, string $userUuid)
    {
        if (!$userUuid) {
            throw new \Exception("Identifiant du user n'est pas renseigné", Response::HTTP_BAD_REQUEST);
        }
        $user = $this->getOneUser($userUuid);

        if (isset($payload['email'])) {
            $user->setEmail($payload['email']);
        }
        if (isset($payload['firstname'])) {
            $user->setFirstname($payload['firstname']);
        }
        if (isset($payload['lastname'])) {
            $user->setLastname($payload['lastname']);
        }
        if (isset($payload['phone'])) {
            $user->setPhone($payload['phone']);
        }
        if (isset($payload['username'])) {
            $user->setUsername($payload['username']);
        }
        if (isset($payload['password'])) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $payload['password']));
        }
        if (isset($payload['theme'])) {
            $user->setTheme($payload['theme']);
        }
        if (isset($payload['jobTitle'])) {
            $user->setJobTitle($payload['jobTitle']);
        }
        if (isset($payload['userConfirmed'])) {
            $user->setUserConfirmed($payload['userConfirmed']);
        }
        if (isset($payload['active'])) {
            $user->setActive($payload['active']);
        }
        if (isset($payload['companyUuid'])) {
            $company = $this->companyService->getOneCompany($payload['companyUuid']);
            $user->setCompany($company);
        }
        if (isset($payload['roles'])) {
            $user->setRoles($payload['roles']);
        }

        $user->setUpdatedAt(new DateTimeImmutable());

        try {
            $this->em->flush();
            $this->em->refresh($user);
            return $user;
        } catch (\Exception $e) {
            throw new \Exception("Une erreur est survenue", $e->getCode());

        }
    }

    public function deleteUser(string $userUuid)
    {
        $user = $this->getOneUser($userUuid);
        try {
            $user->setDeletedAt(new DateTimeImmutable());
            $this->em->flush();
        } catch (\Exception $e) {
            throw new \Exception("Une erreur est survenue", $e->getCode());
        }
    }

    public function deleteUsers(array $payload)
    {
        $keys = array_keys($payload);

        if ($keys !== ['userUuid']) {
            throw new \Exception("Une erreur est survenue", Response::HTTP_BAD_REQUEST);
        }

        foreach ($payload['userUuid'] as $userUuid) {
            $this->deleteUser($userUuid);
        }
    }

    // ---- User Functions ----
    public function getClientAllUsers(string $companyUuid)
    {
        $compagny = $this->companyService->getOneCompany($companyUuid);
        return $this->userRepo->findBy(['company' => $compagny->getId()], ['email' => 'ASC']);

    }

    public function getClientOneUser(string $companyUuid, string $userUuid)
    {
        $compagny = $this->companyService->getOneCompany($companyUuid);

        $user = $this->userRepo->findOneBy(['uuid' => $userUuid, 'company' => $compagny->getId()]);
        if (!$user) {
            throw new \Exception("Utilisateur inconnu", Response::HTTP_NOT_FOUND);
        }

        return $user;
    }

    public function createClientUser(array $payload, string $companyUuid)
    {
        return $this->createUser($payload, $companyUuid);
    }

    public function updateClientUser(array $payload, string $companyUuid, string $userUuid)
    {
        if (!$userUuid) {
            throw new \Exception("Identifiant du user n'est pas renseigné", Response::HTTP_BAD_REQUEST);
        }

        $this->companyService->getOneCompany($companyUuid);

        $user = $this->getOneUser($userUuid);

        if (isset($payload['email'])) {
            $user->setEmail($payload['email']);
        }
        if (isset($payload['firstname'])) {
            $user->setFirstname($payload['firstname']);
        }
        if (isset($payload['lastname'])) {
            $user->setLastname($payload['lastname']);
        }
        if (isset($payload['phone'])) {
            $user->setPhone($payload['phone']);
        }
        if (isset($payload['username'])) {
            $user->setUsername($payload['username']);
        }
        if (isset($payload['theme'])) {
            $user->setTheme($payload['theme']);
        }
        if (isset($payload['jobTitle'])) {
            $user->setJobTitle($payload['jobTitle']);
        }
        if (isset($payload['userConfirmed'])) {
            $user->setUserConfirmed($payload['userConfirmed']);
        }
        if (isset($payload['active'])) {
            $user->setActive($payload['active']);
        }
        if (isset($payload['roles'])) {

            if (in_array('ROLE_SUPER_ADMIN', $payload['roles'])) {
                throw new \Exception("Vous n'avez pas les droits pour effectuer cette action", Response::HTTP_UNAUTHORIZED);
            }

            $user->setRoles($payload['roles']);
        }

        $user->setUpdatedAt(new DateTimeImmutable());

        try {
            $this->em->flush();
            $this->em->refresh($user);
            return $user;
        } catch (\Exception $e) {
            throw new \Exception("Une erreur est survenue", $e->getCode());

        }
    }
}
