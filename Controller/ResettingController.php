<?php

namespace XM\SecurityBundle\Controller;

use FOS\UserBundle\Controller\ResettingController as BaseResettingController;
use Symfony\Component\HttpFoundation\Request;

class ResettingController extends BaseResettingController
{
    /**
     * {@inheritdoc}
     */
    public function resetAction(Request $request, $token)
    {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            return $this->redirectToRoute('fos_user_security_login');
        }

        return parent::resetAction($request, $token);
    }
}