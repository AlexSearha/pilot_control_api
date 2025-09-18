<?php

namespace App\Service;

use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Security\Core\User\UserInterface;

class CookiesService
{
    public function __construct(
        private TokenService $tokenService
    )
    {}

    public function createExpirationCookie(string $cookieName, string $token,string $path = '/',  string $ttl = '+10 minutes', $domaine = null, bool $secure = false )
    {
        $dateTimeFromTtl = (new DateTimeImmutable())->modify($ttl);

        return Cookie::create($cookieName, $token, $dateTimeFromTtl,$path,$domaine, $secure);
    }

    public function createDesactivateCookie(string $cookieName ,string $path = '/', $domaine = null, bool $secure = true )
    {
        $dateTimeFromTtl = new DateTimeImmutable();

        return Cookie::create($cookieName, null , $dateTimeFromTtl, $path, $domaine, $secure);
    }

    public function createTrustedDeviceCookie(UserInterface $user, string $ttl = '+30 days')
    {
        $newYearDate = (new DateTimeImmutable())->modify('+30 days');
        $token =  $this->tokenService->generateExpiredToken($user, 'trusted_device', $ttl);

        return Cookie::create('trusted_device', $token, $newYearDate);
    }
}
