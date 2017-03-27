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

use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Document\Content\Article;
use Integrated\Common\Content\ContentInterface;

use Integrated\Common\Converter\ContainerInterface;
use Integrated\Common\Converter\Type\TypeInterface;

use Integrated\Bundle\ContentBundle\Document\Content\Taxonomy;
use Symfony\Component\Security\Acl\Util\ClassUtils;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentType implements TypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerInterface $container, $data, array $options = [])
    {
        if (!$data instanceof ContentInterface) {
            return; // only process content
        }

        $container->set('id', $data->getContentType() . '-' . $data->getId());

        $container->set('type_name', $data->getContentType());
        $container->set('type_class', ClassUtils::getRealClass($data)); // could be a doctrine proxy object but we need the actual class name.
        $container->set('type_id', $data->getId());

        if ($data instanceof Content) {
            $container->set('pub_active', $data->isPublished(false));
        }

        //Relation field and facet field for taxonomy and commercial relations
        $items = array_merge($data->getRelationsByRelationType('taxonomy')->toArray(),$data->getRelationsByRelationType('commercial')->toArray());
        foreach ($items as $relation) {
            foreach ($relation->getReferences()->toArray() as $content) {
                if ($content instanceof Taxonomy || $content instanceof Article) {
                    $container->add('facet_' . $relation->getRelationId(), $content->getTitle());
                    $container->add('taxonomy_' . $relation->getRelationId() . '_string', $content->getTitle());
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated.content';
    }
}
