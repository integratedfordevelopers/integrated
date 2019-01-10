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

use Integrated\Bundle\ContentBundle\Document\Content\Article;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Document\Content\Taxonomy;
use Integrated\Common\Converter\ContainerInterface;
use Integrated\Common\Converter\Type\TypeInterface;

class TaxonomyType implements TypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerInterface $container, $data, array $options = [])
    {
        if (!$data instanceof Content) {
            return; // only process content
        }

        // Relation field and facet field for taxonomy, commercial and edition relations
        $items = array_merge(
            $data->getRelationsByRelationType('taxonomy')->toArray(),
            $data->getRelationsByRelationType('commercial')->toArray(),
            $data->getRelationsByRelationType('edition')->toArray()
        );

        foreach ($items as $relation) {
            foreach ($relation->getReferences()->toArray() as $content) {
                if (($content instanceof Taxonomy || $content instanceof Article) && $content->getTitle()) {
                    $container->add('facet_'.$relation->getRelationId(), $content->getTitle());
                    $container->add('taxonomy_'.$relation->getRelationId().'_string', $content->getTitle());
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated.taxonomy';
    }
}
