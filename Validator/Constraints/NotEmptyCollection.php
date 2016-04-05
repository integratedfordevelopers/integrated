<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\FormTypeBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @author Johan Liefers <johan@e-active.nl>
 * @Annotation
 */
class NotEmptyCollection extends Constraint
{
    /**
     * @var string
     */
    public $message = 'You have to select at least one item.';
}