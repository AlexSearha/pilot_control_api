<?php

namespace App\Security\Voter;

use App\Entity\CompanyClient;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class CompanyClientVoter extends Voter
{
    public const EDIT   = 'COMPANY_EDIT';
    public const VIEW   = 'COMPANY_VIEW';
    public const CREATE = 'COMPANY_CREATE';
    public const DELETE = 'COMPANY_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::VIEW, self::EDIT, self::CREATE, self::DELETE])
            && $subject instanceof CompanyClient;
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


            case self::VIEW:
                if (in_array('ROLE_MANAGER' , $user->getRoles()) || in_array('ROLE_EMPLOYEE' , $user->getRoles())) {

                    if ($user->getCompany() === null) return false;

                    return $user->getCompany()->getId() === $subject->getId();
                }
                break;

            case self::EDIT:
            case self::CREATE:
            case self::DELETE:
                if (in_array('ROLE_MANAGER' , $user->getRoles())) {

                    if ($user->getCompany() === null) return false;

                    return $user->getCompany()->getId() === $subject->getCompany()->getId();
                }
                break;
        }

        return false;
    }
}
