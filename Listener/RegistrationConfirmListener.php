<?php

namespace XM\SecurityBundle\Listener;

use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RegistrationConfirmListener implements EventSubscriberInterface
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * RegistrationConfirmListener constructor.
     *
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

   /**
    * {@inheritDoc}
    */
    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::REGISTRATION_CONFIRM => 'onRegistrationConfirm',
        ];
    }

    /**
     * Sets the registration date on the user
     *
     * @param  GetResponseUserEvent $event
     * @return void
     */
    public function onRegistrationConfirm(GetResponseUserEvent $event)
    {
        if (null === $event->getResponse()) {
            $url = $this->router->generate('fos_user_security_login');
            $event->setResponse(new RedirectResponse($url));
        }
    }
}
