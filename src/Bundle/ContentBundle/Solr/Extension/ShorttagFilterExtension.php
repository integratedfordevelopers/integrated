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
use Integrated\Common\Converter\ContainerInterface;
use Integrated\Common\Converter\Type\TypeExtensionInterface;

class ShorttagFilterExtension implements TypeExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerInterface $container, $data, array $options = [])
    {
        if (!$data instanceof Content) {
            return;
        }

        if ($container->has('content')) {
            $content = $container->get('content');
            $content = preg_replace('/\[object.*?\]/', '', $content);
            $container->set('content', $content);
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
