<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ImageBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class OnTheFlyFormatConverterConstraint extends Constraint
{
    /**
     * @return string
     */
    public function validatedBy()
    {
        return 'integrated_image.validator_ontheformatflyconverter';
    }
}
