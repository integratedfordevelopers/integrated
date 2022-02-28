<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\FormTypeBundle\DependencyInjection\Compiler;

use ReflectionClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @author Vasil Pascal <developer.optimum@gmail.com>
 */
class RegisterContentStyleParametersPass implements CompilerPassInterface
{
    public const STYLE_FORMAT = 'style_formats';
    public const CONTENT_CSS = 'content_css';
    public const PARAMETER_NAME = 'integrated_content_styles';

    /** @var array */
    private $parameters;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->parameters = [self::CONTENT_CSS => [], self::STYLE_FORMAT => []];

        foreach ($container->getParameter('kernel.bundles') as $name => $class) {
            $this->addParameters(\dirname((new ReflectionClass($class))->getFileName()));
        }

        $container->getParameterBag()->add([self::PARAMETER_NAME => $this->parameters]);
    }

    /**
     * @param $dir
     *
     * @throws FileException
     */
    private function addParameters($dir)
    {
        $filePath = $dir.'/Resources/config/contentstyle/contentstyle.xml';
        if (!is_file($filePath)) {
            return null;
        }

        $content = file_get_contents($filePath);
        $crawler = new Crawler($content);
        $options = $crawler->filter('styles')->children();

        foreach ($options as $option) {
            /** @var $option \DOMElement */
            $type = $option->getAttribute('type');
            if (!\in_array($type, [self::STYLE_FORMAT, self::CONTENT_CSS])) {
                throw new FileException("The file $filePath is not valid");
            }

            if ($type == self::STYLE_FORMAT) {
                $availableFormatParams = ['title', 'inline', 'block', 'selector', 'classes', 'styles', 'attributes', 'exact', 'wrapper'];

                $formatParams = [];
                foreach ($option->childNodes as $formatParam) {
                    if (!$formatParam instanceof \DOMElement) {
                        continue;
                    }

                    /** @var $formatParam \DOMElement */
                    if (!\in_array($formatParam->tagName, $availableFormatParams)) {
                        throw new FileException("The file $filePath is not valid");
                    }

                    $formatParams[$formatParam->tagName] = $formatParam->nodeValue;
                }

                $this->parameters[$type][] = $formatParams;
            } else {
                $this->parameters[$type][] = $option->nodeValue;
            }
        }
    }
}
