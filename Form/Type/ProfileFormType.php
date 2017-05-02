<?php

namespace XM\SecurityBundle\Form\Type;

use FOS\UserBundle\Form\Type\ProfileFormType as FOSProfileFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // username is their email
        $builder->remove('username')
            // we'll re-add the email field below
            ->remove('email');

        $builder
            ->add('email', null, [
                'label' => 'form.email',
                'translation_domain' => 'FOSUserBundle',
                'attr' => ['maxlength' => 180],
            ])
            ->add('firstName', null, [
                'label' => 'form.first_name',
                'translation_domain' => 'FOSUserBundle',
                'attr' => ['maxlength' => 255],
            ])
            ->add('lastName', null, [
                'label' => 'form.last_name',
                'translation_domain' => 'FOSUserBundle',
                'attr' => ['maxlength' => 255],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return FOSProfileFormType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'xm_security_user_profile';
    }
}