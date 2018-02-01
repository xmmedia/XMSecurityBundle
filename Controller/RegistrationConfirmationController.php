<?php

namespace XM\SecurityBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class RegistrationConfirmationController extends Controller
{
    /**
     * Resends the account activation link typically sent during registration.
     *
     * @param Request   $request
     * @param User|null $user
     *
     * @return Response
     */
    public function resendAction(Request $request, User $user = null)
    {
        $currentUser = $this->getUser();

        if (null === $user) {
            $user = $currentUser;
            if (!is_object($user) || !$user instanceof UserInterface) {
                throw new AccessDeniedException(
                    'This user does not have access to this section.'
                );
            }
        } else if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException(
                'You cannot resend the confirmation for another user.'
            );
        }

        $isCurrentUser = $user->getId() === $currentUser->getId();

        // get the referer, if none redirect to the login
        // which should go to the default page after login
        $redirect = $request->headers->get(
            'referer',
            $this->generateUrl('fos_user_security_login')
        );

        if ($user->isEnabled()) {
            if ($isCurrentUser) {
                $this->addFlash('warning', 'Your account is already enabled.');
            } else {
                $this->addFlash('warning', 'The user is already enabled.');

            }

            return $this->redirect($redirect);
        }

        if (null === $user->getConfirmationToken()) {
            throw new \InvalidArgumentException(
                'The user does not have a confirmation token and therefore cannot be activated.'
            );
        }

        $this->get('fos_user.mailer')->sendConfirmationEmailMessage($user);

        if ($isCurrentUser) {
            $this->addFlash(
                'success',
                'Your account activation email has been resent.'
            );
        } else {
            $this->addFlash(
                'success',
                'The account activation email has been resent.'
            );
        }

        return $this->redirect($redirect);
    }
}