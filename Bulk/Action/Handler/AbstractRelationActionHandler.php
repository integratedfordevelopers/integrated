<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Bulk\Action\Handler;

use Doctrine\Common\Collections\Collection;
use Integrated\Bundle\ContentBundle\Bulk\Action\ActionHandlerInterface;
use Integrated\Common\Content\Relation\RelationInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
abstract class AbstractRelationActionHandler implements ActionHandlerInterface
{
    protected function validateOptions(array $options)
    {
        $resolver = new OptionsResolver();

        $resolver->setRequired(['relation', 'references']);
        $resolver->addAllowedTypes('relation', RelationInterface::class);
        $resolver->addAllowedTypes('references', Collection::class); // TODO try to remove this collection

        return $resolver->resolve($options);
    }
}
