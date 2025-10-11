<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class MaintenanceVoter extends Voter
{
    public const EDIT   = 'MAINTENANCE_EDIT';
    public const VIEW   = 'MAINTENANCE_VIEW';
    public const CREATE = 'MAINTENANCE_CREATE';
    public const DELETE = 'MAINTENANCE_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::CREATE, self::DELETE])
            && $subject instanceof \App\Entity\Maintenance;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
            case self::CREATE:
            case self::DELETE:

                if (in_array('ROLE_MANAGER', $user->getRoles())) {
                    return $user->getCompany()->getId() === $subject->getItem()->getCompany()->getId();
                }
                break;

            case self::VIEW:
                if (in_array('ROLE_EMPLOYEE', $user->getRoles()) || in_array('ROLE_MANAGER', $user->getRoles())) {
                    return $user->getCompany()->getId() === $subject->getItem()->getCompany()->getId();
                }
                break;
        }

        return false;
    }
}
