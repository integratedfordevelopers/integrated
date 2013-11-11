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

/**
 * @TODO this class is just a very simple setup
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
abstract class AbstractTranslatable extends AbstractType
{
    /**
     * @TODO: this does not make any sense yet
     * @return array
     */
    public function getLocales()
    {
        return array(
            'en_US' => 'EN',
            'nl_NL' => 'NL'
        );
    }
}