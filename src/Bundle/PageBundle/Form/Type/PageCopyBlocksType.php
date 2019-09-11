<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageCopyBlocksType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['blocks'] as $id => $block) {
            $builder->add('block_'.$id, PageCopyBlockType::class, [
                'block' => $block,
                'channel' => $options['channel'],
                'targetChannel' => $options['targetChannel'],
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['blocks', 'channel', 'targetChannel']);
        $resolver->setAllowedTypes('blocks', 'array');
        $resolver->setAllowedTypes('channel', 'string');
        $resolver->setAllowedTypes('targetChannel', 'string');
    }
}
