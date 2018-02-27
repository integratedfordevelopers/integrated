<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class UniqueEntry extends Constraint
{
    public $message = 'This value is already used.';
    public $fields = [];
    public $caseInsensitive = false;

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions()
    {
        return ['fields'];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption()
    {
        return 'fields';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return Constraint::PROPERTY_CONSTRAINT;
    }
}
