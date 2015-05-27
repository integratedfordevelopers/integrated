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

use Integrated\Bundle\UserBundle\Model\RoleManagerInterface;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RoleType extends AbstractType
{
    /**
     * @var RoleManagerInterface
     */
    private $manager;

    /**
     * Constructor.
     *
     * @param RoleManagerInterface $manager
     */
    public function __construct(RoleManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('class', $this->manager->getClassName());

        $resolver->setDefault('choice_value', 'id');
        $resolver->setDefault('choice_label', 'username');

        $resolver->setDefault('multiple', true);
        $resolver->setDefault('expanded', true);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'entity';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_user_role_choice';
    }

    /**
     * @return RoleManagerInterface
     */
    public function getManager()
    {
        return $this->manager;
    }
}