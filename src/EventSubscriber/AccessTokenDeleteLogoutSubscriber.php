<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class AccessTokenDeleteLogoutSubscriber implements EventSubscriberInterface
{
    public function onLogoutEvent(LogoutEvent $event): void
    {
         $response = $event->getResponse();

        if (!$response) {
            $response = new Response();
            $event->setResponse($response);
        }

        $response->headers->clearCookie('access_token');
        $response->headers->clearCookie('refresh_token');

    }

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogoutEvent',
        ];
    }
}
