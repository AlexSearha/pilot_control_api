<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class SupplierOrderVoter extends Voter
{
    public const EDIT = 'SUPLIER_ORDER_EDIT';
    public const VIEW = 'SUPLIER_ORDER_VIEW';
    public const CREATE = 'SUPLIER_ORDER_CREATE';
    public const DELETE = 'SUPLIER_ORDER_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::CREATE, self::DELETE])
            && $subject instanceof \App\Entity\SupplierOrder;
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
            case self::CREATE:
                if (in_array('ROLE_MANAGER', $user->getRoles())) {
                    return $user->getCompany()->getId() === $subject->getCompany()->getId() && $user->getCompany()->getId() === $subject->getSupplier()->getId();
                }
                break;

            case self::VIEW:
                if (in_array('ROLE_EMPLOYEE', $user->getRoles()) || in_array('ROLE_MANAGER', $user->getRoles())) {
                    return $user->getCompany()->getId() === $subject->getCompany()->getId() && $user->getCompany()->getId() === $subject->getSupplier()->getId();
                }
                break;
        }

        return false;
    }
}
