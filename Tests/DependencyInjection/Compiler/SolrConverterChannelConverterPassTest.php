<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Integrated\Bundle\ContentBundle\DependencyInjection\Compiler\SolrConverterChannelConverterPass;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class SolrConverterChannelConverterPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test process function
     */
    public function testProcess()
    {
        $container = new ContainerBuilder();
        $container->setParameter('integrated_solr.converter.class', 'Converter');

        $this->process($container);
        $this->assertEquals(
            'Integrated\\Bundle\\ContentBundle\\Solr\\Converter\\ChannelConverter',
            $container->getParameter('integrated_solr.converter.class')
        );
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function process(ContainerBuilder $container)
    {
        $solrConverterChannelConverterPass = new SolrConverterChannelConverterPass();
        $solrConverterChannelConverterPass->process($container);
    }
}