<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\DependencyInjection\Compiler;

use ReflectionClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author Vasil Pascal <developer.optimum@gmail.com>
 */
class RegisterRolesParametersPass implements CompilerPassInterface
{
    const PARAMETER_NAME = 'integrated_roles';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $parameters = [];

        foreach ($container->getParameter('kernel.bundles') as $name => $class) {
            $this->addParameters(\dirname((new ReflectionClass($class))->getFileName()), $parameters);
        }

        $container->getParameterBag()->add([self::PARAMETER_NAME => $parameters]);
    }

    /**
     * @param string $dir
     * @param array  $parameters
     */
    private function addParameters($dir, &$parameters)
    {
        $filePath = $dir.'/Resources/config/roles/roles.xml';
        if (!is_file($filePath)) {
            return null;
        }

        $content = file_get_contents($filePath);
        $crawler = new Crawler($content);
        $options = $crawler->filter('roles')->children();

        /** @var $option \DOMElement */
        foreach ($options as $option) {
            if ($option->tagName == 'role') {
                $name = '';
                $label = '';

                foreach ($option->getElementsByTagName('name') as $child) {
                    $name = $child->nodeValue;
                }

                foreach ($option->getElementsByTagName('label') as $child) {
                    $label = $child->nodeValue;
                }

                if (strpos($name, 'ROLE_') == 0) {
                    $parameters[strtoupper($name)] = $label = $label ?: $name;
                }
            }
        }
    }
}
