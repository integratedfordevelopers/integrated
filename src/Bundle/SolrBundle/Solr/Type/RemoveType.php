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

use Integrated\Common\Converter\ContainerInterface;
use Integrated\Common\Converter\Type\TypeInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RemoveType implements TypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerInterface $container, $data, array $options = [])
    {
        foreach ($options as $field) {
            $container->remove($field);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated.remove';
    }
}
