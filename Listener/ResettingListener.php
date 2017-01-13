<?php

namespace XM\SecurityBundle\Listener;

use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class ResettingListener implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * ResettingListener constructor.
     *
     * @param UrlGeneratorInterface $router
     * @param TokenStorage $tokenStorage
     */
    public function __construct(
        UrlGeneratorInterface $router,
        TokenStorage $tokenStorage
    ) {
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::RESETTING_RESET_INITIALIZE => 'checkCurrentUser',
            FOSUserEvents::RESETTING_RESET_SUCCESS    => 'setRedirect',
        );
    }

    /**
     * Sets the redirect to the login form if the logged in user does not match
     * the reset/confirmation token user.
     *
     * @param GetResponseUserEvent $event
     */
    public function checkCurrentUser(GetResponseUserEvent $event)
    {
        $currentUser = $this->tokenStorage->getToken()->getUser();
        $resetUser = $event->getUser();

        if ($currentUser->getId() !== $resetUser->getId()) {
            $this->setRedirecToLogin($event);
        }
    }

    /**
     * Always redirect the user to the login form.
     *
     * @param FormEvent $event
     */
    public function setRedirect(FormEvent $event)
    {
        $this->setRedirecToLogin($event);
    }

    /**
     * Sets the response to a redirect to the login page.
     *
     * @param Event $event
     */
    protected function setRedirecToLogin(Event $event)
    {
        $url = $this->router->generate('fos_user_security_login');
        $event->setResponse(new RedirectResponse($url));
    }
}
