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

use Integrated\Bundle\ContentBundle\Form\DataTransformer\Image as Transformer;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class Image extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new Transformer());
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'integrated_image';
    }

    public function getParent()
    {
        return 'file';
    }
}