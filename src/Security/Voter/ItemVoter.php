<?php

namespace App\Security\Voter;

use App\Entity\Item;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class ItemVoter extends Voter
{
    public const EDIT = 'ITEM_EDIT';
    public const VIEW = 'ITEM_VIEW';
    public const CREATE = 'ITEM_CREATE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::CREATE])
            && $subject instanceof Item;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                break;

            case self::VIEW:
              if (in_array('ROLE_EMPLOYEE', $user->getRoles()) || in_array('ROLE_MANAGER', $user->getRoles())) {
                return $subject->getCompany()->getId() === $user->getCompany()->getId();
              }

                break;
        }

        return false;
    }
}
