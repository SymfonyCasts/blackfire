<?php

namespace App\EventSubscriber;

use Blackfire\Client;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class BlackfireAutoProfileSubscriber implements EventSubscriberInterface
{
    public function onRequestEvent(RequestEvent $event)
    {
        // replace with some conditional logic
        $request = $event->getRequest();
        $shouldProfile = $request->getPathInfo() === '/api/github-organization';

        if ($shouldProfile) {
            $blackfire = new Client();
            $probe = $blackfire->createProbe();
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            RequestEvent::class => 'onRequestEvent',
        ];
    }
}
