<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class Channel extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name'
        );

        $builder->add(
            'domains',
            'integrated_csv_array',
            array(
                'label' => 'Domains (comma separated)'
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'channel';
    }
}