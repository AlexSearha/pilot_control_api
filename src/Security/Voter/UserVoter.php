<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class UserVoter extends Voter
{
    public const EDIT   = 'USER_EDIT';
    public const DELETE = 'USER_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE]) && $subject instanceof User;
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
                if (in_array('ROLE_EMPLOYEE', $user->getRoles())) {
                    return $user->getId() === $subject->getId();
                }

                if (in_array('ROLE_MANAGER', $user->getRoles())) {
                    return $user->getCompany() === $subject->getCompany();
                }

                return false;

            case self::DELETE:
                if (in_array('ROLE_MANAGER', $user->getRoles())) {
                    return $user->getCompany() === $subject->getCompany();
                }

                return false;

        }

        return false;
    }
}
