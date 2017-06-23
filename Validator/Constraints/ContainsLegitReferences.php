<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Validator\Constraints;

use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Symfony\Component\Validator\Constraint;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class ContainsLegitReferences extends Constraint
{
    /**
     * @var string
     */
    public $message = 'The given reference(s) does not all seem to correspond the target of the relation "{{ relation }}".';

    /**
     * @var Relation
     */
    public $relation;
}
