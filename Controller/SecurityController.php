<?php

namespace XM\SecurityBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as BaseSecurityController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class SecurityController extends BaseSecurityController
{
    /**
     * The target path, used during the login and passed to the login view.
     * @var string
     */
    protected $targetPath;

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
        $this->pullTargetPathFromQuery($request);

        return parent::loginAction($request);
    }

    /**
     * {@inheritdoc}
     */
    protected function renderLogin(array $data)
    {
        if ($this->targetPath) {
            $data['target_path'] = $this->targetPath;
        }

        $data['registration_is_enabled'] = $this->isRegistrationEnabled();

        return parent::renderLogin($data);
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

    /**
     * Populates the target path/URL property based on the request query param.
     *
     * @param Request $request
     */
    protected function pullTargetPathFromQuery(Request $request)
    {
        $targetPath = $request->query->get('target_path');

        if ($targetPath) {
            $this->targetPath = $targetPath;
        }
    }

    /**
     * Returns true when registration is enabled.
     *
     * @return bool
     */
    protected function isRegistrationEnabled()
    {
        $routes = $this->container->get('router')->getRouteCollection();

        return (null !== $routes->get('fos_user_registration_register'));
    }
}