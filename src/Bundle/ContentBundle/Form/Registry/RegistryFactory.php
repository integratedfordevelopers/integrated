<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\Registry;

use Integrated\Common\ContentType\Form\Custom\Type;
use Integrated\Common\ContentType\Form\Custom\Type\Registry;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Simple factory for Registry.
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class RegistryFactory
{
    /**
     * @return Registry
     */
    public static function create()
    {
        $registry = new Registry();

        // Todo this must be configurable
        $text = new Type();
        $text
            ->setType(TextType::class)
            ->setName('Text')
        ;

        $textarea = new Type();
        $textarea
            ->setType(TextareaType::class)
            ->setName('Textarea')
        ;

        $checkbox = new Type();
        $checkbox
            ->setType(CheckboxType::class)
            ->setName('Checkbox')
        ;

        $registry
            ->add($text)
            ->add($textarea)
            ->add($checkbox)
        ;

        return $registry;
    }
}
