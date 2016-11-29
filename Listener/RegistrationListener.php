<?php

namespace XM\SecurityBundle\Listener;

use FOS\UserBundle\Doctrine\UserManager;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use XM\MailManagerBundle\Component\MailManager;

class RegistrationListener implements EventSubscriberInterface
{
    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var MailManager
     */
    protected $mailManager;

    /**
     * @var string
     */
    protected $adminEmail;

    /**
     * Constructor.
     *
     * @param UserManager $userManager
     * @param MailManager $mailManager
     * @param $adminEmail
     */
    public function __construct(UserManager $userManager, MailManager $mailManager, $adminEmail)
    {
        $this->userManager = $userManager;
        $this->mailManager = $mailManager;
        $this->adminEmail = $adminEmail;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::REGISTRATION_COMPLETED => [
                ['setRegistrationDate', 10],
                ['emailOnRegistration', 30],
            ],
            FOSUserEvents::REGISTRATION_CONFIRMED => 'incrementLoginCount',
        ];
    }

    /**
     * Sets the registration date on the user entity/record.
     *
     * @param  FilterUserResponseEvent $event
     */
    public function setRegistrationDate(FilterUserResponseEvent $event)
    {
        /** @var \XM\SecurityBundle\Entity $user */
        $user = $event->getUser();

        $user->setRegistrationDate(new \DateTime());
        $this->userManager->updateUser($user);
    }

    /**
     * Emails the admin regarding the registration.
     *
     * @param  FilterUserResponseEvent $event
     */
    public function emailOnRegistration(FilterUserResponseEvent $event)
    {
        $template = '@XMSecurity/Mail/user_registered.html.twig';
        $mailParams = [
            'user' => $event->getUser(),
        ];

        $this->mailManager->getSender()
            ->setTemplate($template, $mailParams)
            ->send($this->adminEmail);
    }

    /**
     * Increment their login account when they confirm their account
     * (which also logs them in).
     *
     * @param FilterUserResponseEvent $event
     */
    public function incrementLoginCount(FilterUserResponseEvent $event)
    {
        /** @var \XM\SecurityBundle\Entity $user */
        $user = $event->getUser();

        $user->incrementLoginCount();

        $this->userManager->updateUser($user);
    }
}