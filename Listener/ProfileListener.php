<?php

namespace XM\SecurityBundle\Listener;

use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProfileListener implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * ProfileListener constructor.
     *
     * @param UrlGeneratorInterface $router
     */
    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::PROFILE_EDIT_SUCCESS    => 'profileEditRedirect',
            FOSUserEvents::CHANGE_PASSWORD_SUCCESS => 'profileEditRedirect',
        ];
    }

    /**
     * Sets the response with a redirect back to the user's profile.
     *
     * @param  FormEvent $event
     * @return void
     */
    public function profileEditRedirect(FormEvent $event)
    {
        $url = $this->router->generate('fos_user_profile_edit');
        $event->setResponse(new RedirectResponse($url));
    }
}