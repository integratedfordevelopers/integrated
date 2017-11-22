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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

use Integrated\Bundle\ContentBundle\Form\DataTransformer\CsvArray as Transformer;

/**
 * Form type which can handle comma separated values and returns an array
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class CsvArray extends AbstractType
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
    public function getBlockPrefix()
    {
        return 'integrated_csv_array';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return TextType::class;
    }
}
