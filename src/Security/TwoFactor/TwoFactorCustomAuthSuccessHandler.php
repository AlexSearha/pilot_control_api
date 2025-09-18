<?php

namespace App\Security\TwoFactor;

use App\Entity\User;
use App\Enum\TwoFactorStatusEnum;
use App\Repository\UserRepository;
use App\Service\AuthService;
use App\Service\CookiesService;
use App\Service\FormatService;
use App\Service\MailerService;
use App\Service\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationSuccessResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class TwoFactorCustomAuthSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    /**
     * @param iterable|JWTCookieProvider[] $cookieProviders
     */
    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
        private EventDispatcherInterface $dispatcher,
        private iterable $cookieProviders = [],
        private bool $removeTokenFromBodyWhenCookiesUsed = true,
        private FormatService $formatService,
        private TokenService $tokenService,
        private CookiesService $cookiesService,
        private UserRepository $userRepo,
        private AuthService $authService,
        private EntityManagerInterface $em,
        private MailerService $mailer
    ) {}



    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        // $user           = $token->getUser();
        // $clientCookies  = $request->cookies->all();
        // $payload        = $request->getPayload()->all();

        // // === LOGIN SUBMIT ===
        // if (isset($payload['step']) && $payload['step'] === 'login') {
        //     // Step 1: Check trusted device
        //     $trustedResponse = $this->checkTrustedDevice($clientCookies, $user);
        //     if ($trustedResponse instanceof Response) {
        //         return $trustedResponse; // success via trusted device
        //     }

        //     // Step 2: Check 2FA cookie
        //     if (!isset($clientCookies['2fa'])) {
        //         // no 2FA cookie: generate a pending one + email + redirect to /2fa_check
        //         $code = $this->authService->generateRandomDigits();
        //         $user->setEmailAuthCode($code);
        //         try {
        //             $this->em->flush();
        //         } catch (\Exception $e) {
        //             return $this->formatService->sendErrorReponse('Une erreur est survenue, veuillez reesayer');
        //         }

        //         // send email with $code
        //         $this->mailer->sendSimpleEmail($code, $user, 'Auth code');

        //         $cookie = $this->createTwoFactorCookie($user, TwoFactorStatusEnum::PENDING, ['authCode' => $code]);

        //         return $this->formatService->sendSuccessReponse(
        //             ['message' => 'Un code vient de vous etre envoyer a votre adresse email' , 'step' => '2fa'],
        //             200,
        //             false,
        //             [$cookie]
        //             );

        //     }

        //     // if cookie is present → validate
        //     if (!$this->tokenService->checkTokenValidity($clientCookies['2fa'])) {
        //         // invalid cookie → remove it and restart login
        //         $invalidateCookie = $this->cookiesService->createDesactivateCookie('2fa');
        //         return $this->formatService->sendSuccessReponse(['step' => 'login'], 200, false, [$invalidateCookie]);
        //     }

        //     // if cookie present and valid
        //     $decodeToken = $this->tokenService->tokenParser($clientCookies['2fa']);
        //     $status = $decodeToken['loginStatus'] ?? null;

        //     return match ($status) {
        //         TwoFactorStatusEnum::PENDING->value => $this->formatService->sendSuccessReponse(['step' => '2fa']),
        //         TwoFactorStatusEnum::VERIFIED->value => $this->handleAuthenticationSuccess($user),
        //         default => $this->formatService->sendSuccessReponse(['step' => 'login']),
        //     };

        // }

        // // === 2FA SUBMIT === //
        // if (isset($payload['step']) && $payload['step'] === '2fa') {

        //     $clientCookies = $request->cookies->all();

        //     if (!isset($clientCookies['2fa']) || !$this->tokenService->checkTokenValidity($clientCookies['2fa'])) {
        //         return $this->formatService->sendSuccessReponse(['step' => 'login']);
        //     }

        //     $decodeToken = $this->tokenService->tokenParser($clientCookies['2fa']);

        //     $status = $decodeToken['loginStatus'] ?? null;

        //     if ($status === TwoFactorStatusEnum::PENDING->value) {

        //         $allCookies = [
        //             $this->cookiesService->createDesactivateCookie('2fa'),
        //             $this->cookiesService->createTrustedDeviceCookie($user)
        //         ];

        //         return $this->checkCodeValidity($user, $decodeToken['authCode'], $payload['authCode'] ?? null, $allCookies);
        //     }

        //     if ($status === TwoFactorStatusEnum::VERIFIED->value) {
        //         return $this->handleAuthenticationSuccess($user);
        //     }

        //     return $this->formatService->sendSuccessReponse(['step' => 'login']);
        // }

        // fallback: default lexik flow
        return $this->handleAuthenticationSuccess($token->getUser());
    }

    public function handleAuthenticationSuccess(UserInterface $user, $jwt = null, array $extraCookies = []): Response
    {
        if (null === $jwt) {
            $jwt = $this->jwtManager->create($user);
        }

        $jwtCookies = [];
        foreach ($this->cookieProviders as $cookieProvider) {
            $jwtCookies[] = $cookieProvider->createCookie($jwt);
        }

        dd($jwtCookies);

        $response = new JWTAuthenticationSuccessResponse($jwt, [], $jwtCookies);
        $event = new AuthenticationSuccessEvent(['token' => $jwt], $user, $response);

        $this->dispatcher->dispatch($event, Events::AUTHENTICATION_SUCCESS);
        $responseData = $event->getData();

        if ($jwtCookies && $this->removeTokenFromBodyWhenCookiesUsed) {
            unset($responseData['token']);
        }

        if (count($extraCookies) > 0) {
            foreach ($extraCookies as $cookie) {
                $response->headers->setCookie($cookie);
            }
        }

        if ($responseData) {
            $response->setData($responseData);
        } else {
            $response->setStatusCode(Response::HTTP_NO_CONTENT);
        }

        return $response;
    }

        private function createTwoFactorCookie(UserInterface $user, TwoFactorStatusEnum $state, array $extraPayload = []): Cookie
    {
        $newToken = $this->tokenService->generateExpiredToken(
            $user,
            '2fa',
            extraPayload: array_merge(['loginStatus' => $state->value], $extraPayload)
        );

        return $this->cookiesService->createExpirationCookie("2fa", $newToken, secure: true);
    }

    private function checkTrustedDevice(array $cookies, UserInterface $user): ?Response
    {
        if (!isset($cookies['trusted_device'])) {
            return null; // no trusted device cookie, fallback to 2FA
        }

        // Here you would normally validate the trusted_device token (expiry, signature, etc.)
        // If invalid: return null; otherwise return Lexik flow
        return $this->handleAuthenticationSuccess($user);
    }

    private function checkCodeValidity(User $user, string $tokenAuthCode, $inputClientAuthCode, array $cookies) :Response
    {
        if ($user->getEmailAuthCode() === $inputClientAuthCode && $tokenAuthCode === $inputClientAuthCode) {
            return $this->handleAuthenticationSuccess($user, extraCookies: $cookies);
        }

        return $this->formatService->sendErrorReponse('Code invalide');
    }
}
