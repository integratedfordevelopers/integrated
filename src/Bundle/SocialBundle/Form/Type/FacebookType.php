<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SocialBundle\Form\Type;

use Facebook\Facebook;
use Integrated\Bundle\SocialBundle\Form\EventListener\AddFacebookPageFieldListener;
use Integrated\Bundle\SocialBundle\Form\EventListener\SetFacebookPageTokenListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FacebookType extends AbstractType
{
    /**
     * @var Facebook
     */
    private $facebook;

    public function __construct(Facebook $facebook)
    {
        $this->facebook = $facebook;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $facebook = $this->facebook;

        $builder->addEventSubscriber(new AddFacebookPageFieldListener($facebook));
        $builder->addEventSubscriber(new SetFacebookPageTokenListener($facebook));
        $builder->add('token', TextType::class, ['attr' => ['readonly' => 'true']]);
        $builder->add('apiStatus', TextType::class, ['attr' => ['readonly' => 'true']]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_social_facebook';
    }
}
