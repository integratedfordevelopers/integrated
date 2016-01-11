<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Solr\Type;

use Symfony\Component\PropertyAccess\PropertyAccess;

use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Common\Converter\ContainerInterface;
use Integrated\Common\Converter\Type\TypeInterface;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class RelationJsonType implements TypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerInterface $container, $data, array $options = [])
    {
        if (!($data instanceof Content)) {
            return;
        }
        $relations = $data->getReferencesByRelationId($options['relation_id']);

        $accessor = PropertyAccess::createPropertyAccessor();

        $array = [];
        $i = 0;
        foreach ($relations as $relation) {
            foreach ($options['properties'] as $alias => $property) {
                $array[$i][$alias] = $accessor->getValue($relation, $property);
            }
            $i++;
        }

        $container->set(
            $options['alias'],
            json_encode($array)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated.relation_json';
    }
}
