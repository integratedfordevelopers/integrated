<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Bulk\ActionHandler;

use Doctrine\Common\Collections\Collection;
use Integrated\Common\Content\Relation\RelationInterface;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
abstract class RelationActionHandler implements ActionHandlerInterface
{
    /**
     * @param array $options
     */
    protected function validateOptions(array $options){
        if(!key_exists('relation', $options) || !key_exists('references', $options)) {
            throw new \RuntimeException('This' . __CLASS__ . 'needs a "relation" and "references" as options');
        }

        if(!$options['relation'] instanceof RelationInterface ){
            throw new \RuntimeException('$options[\'relation\'] does not contain a Relation for ' . __CLASS__);
        }

        if(!$options['references'] instanceof Collection){
            throw new \RuntimeException('$options[\'relation\'] does not contain a Collection for ' . __CLASS__);
        }
    }
}