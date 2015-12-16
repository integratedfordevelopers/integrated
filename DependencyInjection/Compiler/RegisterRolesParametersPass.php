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
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @author Vasil Pascal <developer.optimum@gmail.com>
 */
class RegisterRolesParametersPass implements CompilerPassInterface
{
    const PARAMETER_NAME = "integrated_roles";

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $parameters = [];

        foreach ($container->getParameter('kernel.bundles') as $name => $class) {
            $this->addParameters(dirname((new ReflectionClass($class))->getFileName()), $parameters);
        }

        $container->getParameterBag()->add([self::PARAMETER_NAME => $parameters]);
    }

    /**
     * @param $dir
     * @param $parameters
     * @return null
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
            if ($option->tagName == 'role' && strpos($option->nodeValue, 'ROLE_') == 0) {
                $roleUpper = strtoupper($option->nodeValue);
                $parameters[$roleUpper] = $roleUpper;
            }
        }
    }
}
