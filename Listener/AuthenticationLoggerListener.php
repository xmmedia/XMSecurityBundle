<?php

namespace XM\SecurityBundle\Listener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\HttpFoundation\RequestStack;
use FOS\UserBundle\Doctrine\UserManager;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * Idea from:
 * http://stackoverflow.com/questions/11180351/symfony2-after-successful-login-event-perform-set-of-actions
 */
class AuthenticationLoggerListener implements EventSubscriberInterface
{
    protected $tokenStorage;
    protected $requestStack;
    protected $userManager;
    protected $em;
    protected $authLogClass;

    /**
     * AuthenticationLoggerListener constructor.
     *
     * @param TokenStorage $tokenStorage
     * @param RequestStack $requestStack
     * @param UserManager $userManager
     * @param ObjectManager $em
     * @param string $authLogClass
     */
    public function __construct(
        TokenStorage $tokenStorage,
        RequestStack $requestStack,
        UserManager $userManager,
        ObjectManager $em,
        $authLogClass
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
        $this->userManager = $userManager;
        $this->em = $em;
        $this->authLogClass = $authLogClass;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN            => 'logSuccess',
            FOSUserEvents::RESETTING_RESET_COMPLETED     => 'logSuccess',
            FOSUserEvents::REGISTRATION_CONFIRMED        => 'logSuccess',
            AuthenticationEvents::AUTHENTICATION_FAILURE => 'logFailure',
        ];
    }

    /**
     * Logs the successful login.
     * Event will be passed but we don't use it or care what it is.
     */
    public function logSuccess()
    {
        /** @var XM\SecurityBundle\Entity\User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user instanceof \XM\SecurityBundle\Entity\User) {
            $user->incrementLoginCount();

            $this->userManager->updateUser($user);

            $this->addLogSuccess($user);
        }
    }

    /**
     * Logs a failed login.
     *
     * @param AuthenticationFailureEvent $event
     */
    public function logFailure(AuthenticationFailureEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();
        $exceptionMsg = $event->getAuthenticationException()
            ->getPrevious()
            ->getMessage();

        $authLog = $this->createAuthLog();
        $authLog->setSuccess(false);
        $authLog->setUsername($request->request->get('_username'));
        $authLog->setMessage($exceptionMsg);

        $this->em->persist($authLog);
        $this->em->flush();
    }

    /**
     * Creates the auth log entity with the User Agent and client IP.
     *
     * @return AuthLog
     */
    protected function createAuthLog()
    {
        $request = $this->requestStack->getCurrentRequest();

        /** @var XM\SecurityBundle\Entity\AuthLog $authLog */
        $authLog = new $this->authLogClass();
        $authLog->setDatetime(new \DateTimeImmutable());
        $authLog->setUserAgent($request->headers->get('User-Agent'));
        $authLog->setIpAddress($request->getClientIp());

        return $authLog;
    }

    /**
     * Add successful log.
     *
     * @param UserInterface $user
     */
    protected function addLogSuccess(UserInterface $user)
    {
        $this->userManager->updateUser($user);

        $authLog = $this->createAuthLog();
        $authLog->setSuccess(true);
        $authLog->setUser($user);
        $authLog->setUsername($user->getUsername());

        $this->em->persist($authLog);
        $this->em->flush();
    }
}