<?php

namespace XM\SecurityBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as BaseSecurityController;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends BaseSecurityController
{
    public function loginAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $response = $this->get('xm_security.auth.authentication_success_handler')
                ->onAuthenticationSuccess($request, $this->get('security.token_storage')->getToken());

            return $response;
        }

        return parent::loginAction($request);
    }
}