<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Form\Type;

use Integrated\Bundle\FormTypeBundle\Form\Type\IpAddressType;
use Integrated\Bundle\UserBundle\Model\IpListManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IpListFormType extends AbstractType
{
    /**
     * @var IpListManagerInterface
     */
    private $manager;

    public function __construct(IpListManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('ip', IpAddressType::class);
        $builder->add('description', TextareaType::class, [
            'required' => false,
            'empty_data' => '',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('empty_data', function (FormInterface $form) {
            if ($form->get('ip')->getData()) {
                return $this->getManager()->create(
                    $form->get('ip')->getData(),
                    $form->get('description')->getData()
                );
            }

            return null;
        });

        $resolver->setDefault('data_class', $this->getManager()->getClassName());
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_user_iplist_form';
    }

    /**
     * @return IpListManagerInterface
     */
    public function getManager()
    {
        return $this->manager;
    }
}
