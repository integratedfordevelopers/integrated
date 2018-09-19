<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\FormConfig\Field;

use Doctrine\Common\Persistence\ManagerRegistry;
use Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded\Field\RelationField;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\FormConfig\FormConfigFieldProviderInterface;

class RelationProvider implements FormConfigFieldProviderInterface
{
    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields(ContentTypeInterface $type): array
    {
        $relations = $this->registry->getRepository(Relation::class)->createQueryBuilder()
            ->field('sources.$id')->equals($type->getId())
            ->sort(['name' => 'ASC'])
            ->getQuery()
            ->getIterator();

        $fields = [];

        foreach ($relations as $relation) {
            $fields[] = new RelationField($relation);
        }

        return $fields;
    }
}
