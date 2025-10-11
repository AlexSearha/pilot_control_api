<?php

namespace App\Security\Voter;

use App\Entity\Company;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class CompanyVoter extends Voter
{
    public const EDIT       = 'COMPANY_EDIT';
    public const VIEW       = 'COMPANY_VIEW';
    public const CREATE     = 'COMPANY_CREATE';
    public const LIST_ALL   = 'COMPANY_LIST_ALL';
    public const DELETE     = 'COMPANY_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::CREATE, self::LIST_ALL, self::DELETE])
            && $subject instanceof Company;
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
            case self::LIST_ALL:
            case self::CREATE:
            case self::DELETE:
                if (in_array('ROLE_MANAGER', $user->getRoles())) {
                    if (!$user->getCompany()) {
                        return false;
                    }

                    return $user->getCompany()->getId()  === $subject->getId();
                }

                break;

            case self::VIEW:
                if (in_array('ROLE_EMPLOYEE', $user->getRoles()) || in_array('ROLE_MANAGER', $user->getRoles())) {
                    if (!$user->getCompany()) {
                        return false;
                    }

                    return $user->getCompany()->getId() === $subject->getId();
                }

                break;
        }

        return false;
    }
}
