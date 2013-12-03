<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\Type\Translatable;

use Integrated\Bundle\ContentBundle\Form\Type\AbstractTranslatable;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @TODO this class is just a very simple setup
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class Textarea extends AbstractTranslatable
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->getLocales() as $locale => $label) {
            $builder->add(
                $locale,
                'textarea',
                array(
                    'label' => $label
                )
            );
        }

        $builder->addModelTransformer($this->getDefaultTransformer());
    }

    public function getName()
    {
        return 'translatable_textarea';
    }
}