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

use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Converter\ContainerInterface;
use Integrated\Common\Converter\Type\TypeInterface;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class PropertyType implements TypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerInterface $container, $data, array $options = [])
    {
        if (!($data instanceof ContentInterface)) {
            return;
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($options as $condition) {
            if (isset($condition['fieldValue'])) {
                if ($condition['fieldValue'] == $accessor->getValue($data, $condition['field'])) {
                    $container->add('facet_properties', $condition['label']);
                }
            } elseif (isset($condition['fieldValueNot'])) {
                if ($condition['fieldValueNot'] != $accessor->getValue($data, $condition['field'])) {
                    $container->add('facet_properties', $condition['label']);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated.properties';
    }
}
