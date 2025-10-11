<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class SupplierVoter extends Voter
{
    public const EDIT   = 'SUPPLIER_EDIT';
    public const VIEW   = 'SUPPLIER_VIEW';
    public const DELETE = 'SUPPLIER_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof \App\Entity\Supplier;
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
            case self::DELETE:
            case self::EDIT:
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
