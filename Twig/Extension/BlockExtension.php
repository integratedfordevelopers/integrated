<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\BlockBundle\Twig\Extension;

use Symfony\Bundle\FrameworkBundle\Templating\TemplateNameParser;

use Integrated\Common\Block\BlockHandlerRegistryInterface;
use Integrated\Common\Block\BlockInterface;
use Integrated\Bundle\BlockBundle\Block\BlockHandler;
use Integrated\Bundle\BlockBundle\Locator\LayoutLocator;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class BlockExtension extends \Twig_Extension
{
    /**
     * @var BlockHandlerRegistryInterface
     */
    private $registry;

    /**
     * @var LayoutLocator
     */
    private $locator;

    /**
     * @var TemplateNameParser
     */
    private $parser;

    /**
     * @param BlockHandlerRegistryInterface $registry
     * @param LayoutLocator $locator
     * @param TemplateNameParser $parser
     */
    public function __construct(BlockHandlerRegistryInterface $registry, LayoutLocator $locator, TemplateNameParser $parser)
    {
        $this->registry = $registry;
        $this->locator = $locator;
        $this->parser = $parser;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('integrated_block', [$this, 'renderBlock'], ['is_safe' => ['html'], 'needs_environment' => true]),
        ];
    }

    /**
     * @param \Twig_Environment $environment
     * @param BlockInterface $block
     * @param string $template
     * @return string
     */
    public function renderBlock(\Twig_Environment $environment, $block, $template = null)
    {
        if ($block instanceof BlockInterface) {

            $handler = $this->registry->getHandler($block->getType());

            if ($handler instanceof BlockHandler) {
                $handler->setTwig($environment);

                $layouts = $this->locator->getLayouts($block->getType());

                if ($template) {

                    //$template = 'AppBundle:themes:gim/blocks/text/base.html.twig';

                    $reference = $this->parser->parse($template);

                    if (preg_match('|(.+)/blocks/(.+)/(.+)|i', $reference->get('name'), $match)) {

                    } else {
                        // form website page theme
                    }

                    // check block layout
                    // check site theme > add block layout

                    // search in AppBundle:themes:gim:view > *:themes:gim:view > *:themes:default:view > *:themes:default:default

                    //$handler->setTemplate($reference->getLogicalName());
                }

                return $handler->execute($block);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_block_block';
    }
}
