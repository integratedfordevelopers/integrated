<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\FormTypeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

class TinyMCEType extends AbstractType
{

    public function getParent()
    {
        return 'textarea';
    }

    public function getName()
    {
        return 'integrated_tinymce';
    }

}