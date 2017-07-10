<?php
namespace XM\SecurityBundle\Handler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;

class AuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{
    /**
     * @var AuthorizationChecker
     */
    protected $authChecker;

    /**
     * AuthenticationSuccessHandler constructor.
     *
     * @param HttpUtils $httpUtils
     * @param array $options
     * @param AuthorizationChecker $authChecker
     */
    public function __construct(
        HttpUtils $httpUtils,
        array $options,
        AuthorizationChecker $authChecker
    ) {
        parent::__construct($httpUtils, $options);

        $this->authChecker = $authChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token
    ) {
        $this->setDefaultTargetPath();

        return parent::onAuthenticationSuccess($request, $token);
    }

    /**
     * Sets the default target path option based on the user's roles,
     * if applicable.
     */
    protected function setDefaultTargetPath()
    {
        if ($this->authChecker->isGranted('ROLE_ADMIN')) {
            $this->options['default_target_path'] = 'admin_dashboard';
        }
    }
}