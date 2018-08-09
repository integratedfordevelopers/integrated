<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded;

use Integrated\Common\Content\Relation\RelationInterface;
use Integrated\Common\FormConfig\FormConfigFieldInterface;

class RelationField implements FormConfigFieldInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $options;

    /**
     * @var RelationInterface
     */
    private $relation;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return RelationField
     */
    public function setName(string $name): RelationField
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return RelationField
     */
    public function setType(string $type): RelationField
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return RelationField
     */
    public function setOptions(array $options): RelationField
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return RelationInterface
     */
    public function getRelation(): RelationInterface
    {
        return $this->relation;
    }

    /**
     * @param RelationInterface $relation
     * @return RelationField
     */
    public function setRelation(RelationInterface $relation): RelationField
    {
        $this->relation = $relation;

        return $this;
    }
}
