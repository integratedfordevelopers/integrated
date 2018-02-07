<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Solr\Type;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Common\Converter\ContainerInterface;
use Integrated\Common\Converter\Type\TypeInterface;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class RelationJsonType implements TypeInterface
{
    /**
     * @var PropertyAccessorInterface
     */
    protected $accessor;

    /**
     * Constructor.
     *
     * @param PropertyAccessorInterface $accessor
     */
    public function __construct(PropertyAccessorInterface $accessor = null)
    {
        $this->accessor = $accessor ?: PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerInterface $container, $data, array $options = [])
    {
        if (!($data instanceof Content)) {
            return;
        }

        $relations = $data->getReferencesByRelationId($options['relation_id']);

        $array = [];

        foreach ($relations as $key => $relation) {
            foreach ($options['properties'] as $alias => $property) {
                $array[$key][$alias] = $this->accessor->getValue($relation, $property);
            }
        }

        $container->set($options['alias'], json_encode($array));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated.relation_json';
    }
}
