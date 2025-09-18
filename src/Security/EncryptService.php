<?php

namespace App\Security;

use Symfony\Component\PasswordHasher\PasswordHasherInterface;
class EncryptService
{
    public function __construct(
        private PasswordHasherInterface $passwordHasher
    )
    {}


}
