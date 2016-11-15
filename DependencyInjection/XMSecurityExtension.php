<?php

namespace XM\SecurityBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class XMSecurityExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $config = [
            'user_class' => 'XM\SecurityBundle\Entity\User',
            'registration' => [
                'confirmation' => [
                    'enabled' => true,
                ],
                'form' => [
                    'type' => 'XM\SecurityBundle\Form\Type\RegistrationFormType',
                    'validation_groups' => ['RZegistration'],
                ]
            ],
            'profile' => [
                'form' => [
                    'type' => 'XM\SecurityBundle\Form\Type\ProfileFormType',
                    'validation_groups' => ['Profile'],
                ]
            ],
        ];

        $container->prependExtensionConfig('fos_user', $config);
    }
}
