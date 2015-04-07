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

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class CopyAppendType extends CopyType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated.copy.append';
    }

    /**
     * @param ContainerInterface $container
     * @param string             $field
     */
    protected function remove(ContainerInterface $container, $field)
    {
        // do not remove anything as the values should be appended.
    }
}
