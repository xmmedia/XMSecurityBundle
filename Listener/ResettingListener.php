<?php

namespace XM\SecurityBundle\Listener;

use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ResettingListener implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * ResettingListener constructor.
     *
     * @param UrlGeneratorInterface $router
     * @param int                   $tokenTtl
     */
    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::RESETTING_RESET_SUCCESS => 'setRedirect',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function setRedirect(FormEvent $event)
    {
        $url = $this->router->generate('fos_user_security_login');
        $event->setResponse(new RedirectResponse($url));
    }
}
