<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Solr\Extension;

use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Common\ContentType\ResolverInterface;
use Integrated\Common\Converter\ContainerInterface;
use Integrated\Common\Converter\Type\TypeExtensionInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class PubActiveExtension implements TypeExtensionInterface
{
    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @param ResolverInterface $resolver
     */
    public function __construct(ResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerInterface $container, $data, array $options = [])
    {
        if (!$data instanceof Content) {
            return;
        }

        $published = $data->isPublished(false);
        $contentType = $this->resolver->getType($data->getContentType());
        if ($contentType->getOption('publication') === 'disabled') {
            $published = false;
        }

        $container->set('pub_active', $published);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated.content';
    }
}
