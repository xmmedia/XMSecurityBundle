<?php

namespace XM\SecurityBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as BaseSecurityController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class SecurityController extends BaseSecurityController
{
    /**
     * {@inheritdoc}
     */
    public function loginAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $token = $this->get('security.token_storage')->getToken();
            $response = $this->get('xm_security.auth.authentication_success_handler')
                ->onAuthenticationSuccess($request, $token);

            return $response;
        }

        $this->pullUsernameFromQuery($request);

        return parent::loginAction($request);
    }

    /**
     * Populates the last username in the session based on
     * the query param "username".
     *
     * @param Request $request
     */
    protected function pullUsernameFromQuery(Request $request)
    {
        $queryUsername = $request->query->get('username');

        if ($queryUsername) {
            if (strlen($queryUsername) <= Security::MAX_USERNAME_LENGTH) {
                $request->getSession()
                    ->set(Security::LAST_USERNAME, $queryUsername);
            }
        }
    }
}