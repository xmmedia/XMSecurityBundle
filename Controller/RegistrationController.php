<?php

namespace XM\SecurityBundle\Controller;

use FOS\UserBundle\Controller\RegistrationController as BaseRegistrationController;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends BaseRegistrationController
{
    public function registerAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect($this->generateUrl('index'));
        }

        return parent::registerAction($request);
    }

    public function confirmAction(Request $request, $token)
    {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            return $this->redirectToRoute('fos_user_security_login');
        }

        return parent::confirmAction($request, $token);
    }
}