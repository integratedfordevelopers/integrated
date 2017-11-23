<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Integrated\Bundle\ImageBundle\Validator\Constraints\OnTheFlyFormatConverterConstraint;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class ImageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'constraints' => [new OnTheFlyFormatConverterConstraint()],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return FileType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_image';
    }
}
