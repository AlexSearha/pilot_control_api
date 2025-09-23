<?php

namespace App\Security\Voter;

use App\Entity\Project;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class ProjectVoter extends Voter
{
    public const EDIT   = 'PROJECT_EDIT';
    public const VIEW   = 'PROJECT_VIEW';
    public const DELETE = 'PROJECT_DELETE';
    public const CREATE = 'PROJECT_CREATE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::CREATE, self::DELETE])
            && $subject instanceof Project;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
            case self::DELETE:
                // authorisation only if user is part on the project list
                if (in_array('ROLE_MANAGER', $user->getRoles())) {

                    if ($user->getCompany()->getId() === $subject->getCompany()->getId()) {

                        foreach ($subject->getUser() as $authorizedUser) {

                            if ($authorizedUser->getId() === $user->getId()) {
                                return true;
                            }
                        }
                    }
                }

                break;

            case self::CREATE:

                if (in_array('ROLE_MANAGER', $user->getRoles())) {
                    return $user->getCompany()->getId() === $subject->getCompany()->getId();
                }
                break;

            case self::VIEW:

                if (in_array('ROLE_EMPLOYEE', $user->getRoles()) || in_array('ROLE_MANAGER', $user->getRoles())) {
                    return $user->getCompany()->getId() === $subject->getCompany()->getId();
                }
                break;
        }

        return false;
    }
}
