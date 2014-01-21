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

use Integrated\Bundle\SolrBundle\DependencyInjection\CompilerPass\RegisterConverterFileReaderMappingPass;
use Integrated\Bundle\SolrBundle\DependencyInjection\IntegratedSolrExtension;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class IntegratedSolrBundle extends Bundle
{
	/**
	 * @inheritdoc
	 */
	public function build(ContainerBuilder $container)
	{
		$container->addCompilerPass(new RegisterConverterFileReaderMappingPass());
	}

    /**
     * @inheritdoc
     */
    public function getContainerExtension()
    {
        return new IntegratedSolrExtension();
    }
}