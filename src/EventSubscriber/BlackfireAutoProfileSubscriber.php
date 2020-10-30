<?php

namespace App\EventSubscriber;

use Blackfire\Client;
use Blackfire\Probe;
use Blackfire\Profile\Configuration;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

class BlackfireAutoProfileSubscriber implements EventSubscriberInterface
{
    /**
     * @var Probe|null
     */
    private $probe;

    public function onRequestEvent(RequestEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        // replace with some conditional logic
        $request = $event->getRequest();
        $shouldProfile = $request->getPathInfo() === '/api/github-organization';

        if ($shouldProfile) {
            $configuration = new Configuration();
            $configuration->setTitle('Automatic GitHub org profile');
            $blackfire = new Client();
            $this->probe = $blackfire->createProbe($configuration);
        }
    }

    public function onTerminateEvent(TerminateEvent $event)
    {
        if ($this->probe) {
            $this->probe->close();
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            // warning: adding a priority will run before routing & security
            RequestEvent::class => ['onRequestEvent', 1000],
            TerminateEvent::class => 'onTerminateEvent',
        ];
    }
}
