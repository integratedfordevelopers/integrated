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
use Symfony\Component\OptionsResolver\OptionsResolver;

use Integrated\Bundle\ContentBundle\Document\Channel\Channel;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class PrimaryChannelType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Channel::class,
            'choice_label' => 'name',
            'required' => false,
            'attr' => ['class' => 'primary-channel']
        ]);
    }

    public function getParent()
    {
        return 'document';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_primary_channel';
    }
}
