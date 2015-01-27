<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Integrated\Bundle\ContentBundle\Solr\Converter\ChannelConverter;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class SolrConverterChannelConverterPass implements CompilerPassInterface
{
    protected $converter = 'Integrated\\Bundle\\ContentBundle\\Solr\\Converter\\ChannelConverter';

    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasParameter('integrated_solr.converter.class')) {
            $container->setParameter('integrated_solr.converter.class', $this->converter);
        }
    }
}