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
            'bootstrap_collection',
            array(
                'label'              => "Domains (http://site.com)",
                'allow_add'          => true,
                'allow_delete'       => true,
                'add_button_text'    => 'Add domain',
                'delete_button_text' => 'Delete domain',
                'sub_widget_col'     => 5,
                'button_col'         => 3,
                'type'               => 'channel_domain'
            )
        );

        $builder->add('primaryDomain', 'hidden');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'channel';
    }
}