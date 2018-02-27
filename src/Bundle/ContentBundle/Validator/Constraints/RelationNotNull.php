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

use Symfony\Component\Validator\Constraint;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class RelationNotNull extends Constraint
{
    /**
     * @var string
     */
    public $message = 'The relation "{{ relation }}" is required.';

    /**
     * @var string
     */
    public $relation;

    /**
     * @return string
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * @param string $relation
     *
     * @return $this
     */
    public function setRelation($relation)
    {
        $this->relation = $relation;

        return $this;
    }
}
