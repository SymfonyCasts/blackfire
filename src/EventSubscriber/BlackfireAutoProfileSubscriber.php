<?php

namespace App\EventSubscriber;

use Blackfire\Client;
use Blackfire\Probe;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

class BlackfireAutoProfileSubscriber implements EventSubscriberInterface
{
    public function onRequestEvent(RequestEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

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
