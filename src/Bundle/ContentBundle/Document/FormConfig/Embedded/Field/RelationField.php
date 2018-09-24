<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded\Field;

use Integrated\Common\Content\Relation\RelationInterface;
use Integrated\Common\FormConfig\FormConfigFieldInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class RelationField implements FormConfigFieldInterface
{
    /**
     * @var RelationInterface
     */
    private $relation;

    /**
     * @var array
     */
    private $options;

    /**
     * @param RelationInterface $relation
     * @param array             $options
     */
    public function __construct(RelationInterface $relation, array $options = [])
    {
        $this->relation = $relation;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->relation->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return HiddenType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return RelationInterface
     */
    public function getRelation(): RelationInterface
    {
        return $this->relation;
    }
}
