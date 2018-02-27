<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle;

use Integrated\Bundle\SolrBundle\DependencyInjection\CompilerPass\RegisterConfigFileProviderPass;
use Integrated\Bundle\SolrBundle\DependencyInjection\CompilerPass\RegisterTaskHandlerPass;
use Integrated\Bundle\SolrBundle\DependencyInjection\CompilerPass\RegisterTypePass;
use Integrated\Bundle\SolrBundle\DependencyInjection\IntegratedSolrExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class IntegratedSolrBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterConfigFileProviderPass());
        $container->addCompilerPass(new RegisterTypePass());
        $container->addCompilerPass(new RegisterTaskHandlerPass());

        $container->addCompilerPass(new RegisterListenersPass(
            'integrated_solr.event.dispatcher',
            'integrated_solr.event_listener',
            'integrated_solr.event_subscriber'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new IntegratedSolrExtension();
    }
}
