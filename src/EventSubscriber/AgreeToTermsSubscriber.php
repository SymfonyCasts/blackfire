<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Form\AgreeToUpdatedTermsFormType;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupInterface;
use Twig\Environment;

class AgreeToTermsSubscriber implements EventSubscriberInterface, ServiceSubscriberInterface
{
    private $security;
    private $container;

    public function __construct(Security $security, ContainerInterface $container)
    {
        $this->security = $security;
        $this->container = $container;
    }

    public function onRequestEvent(RequestEvent $event)
    {
        $user = $this->security->getUser();

        // only need this for authenticated users
        if (!$user instanceof User) {
            return;
        }

        // in reality, you would hardcode the most recent "terms" date
        // change so you can see if the user needs to "re-agree". I've
        // set it dynamically to 1 year ago to avoid anyone hitting
        // this - as it's just example code...
        //$latestTermsDate = new \DateTimeImmutable('2019-10-15');
        $latestTermsDate = new \DateTimeImmutable('-1 year');

        // user is up-to-date!
        if ($user->getAgreedToTermsAt() >= $latestTermsDate) {
            return;
        }

        $form = $this->container->get(FormFactoryInterface::class)->create(AgreeToUpdatedTermsFormType::class);

        $html = $this->container->get(Environment::class)->render('main/agreeUpdatedTerms.html.twig', [
            'form' => $form->createView()
        ]);
        // resets Encore assets so they render correctly later
        // only technically needed here because we should really
        // "exit" this function before rendering the template if
        // we know the user doesn't need to see the form!
        $this->container->get(EntrypointLookupInterface::class)->reset();

        $response = new Response($html);
        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return [
            RequestEvent::class => 'onRequestEvent',
        ];
    }

    public static function getSubscribedServices()
    {
        return [
            FormFactoryInterface::class,
            Environment::class,
            EntrypointLookupInterface::class,
        ];
    }
}
