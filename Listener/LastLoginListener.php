<?php

namespace XM\SecurityBundle\Listener;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LastLoginListener implements EventSubscriberInterface
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * LastLoginListener constructor.
     *
     * @param UserManagerInterface $userManager
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::RESETTING_RESET_COMPLETED => 'onReset',
        ];
    }

    /**
     * @param FilterUserResponseEvent $event
     */
    public function onReset(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();

        $user->setLastLogin(new \DateTime());
        $this->userManager->updateUser($user);
    }
}
