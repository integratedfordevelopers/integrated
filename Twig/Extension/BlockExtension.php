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

use Integrated\Common\Block\BlockHandlerRegistryInterface;
use Integrated\Common\Block\BlockInterface;
use Integrated\Bundle\ThemeBundle\Templating\ThemeManager;
use Integrated\Bundle\BlockBundle\Block\BlockHandler;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class BlockExtension extends \Twig_Extension
{
    /**
     * @var BlockHandlerRegistryInterface
     */
    private $blockRegistry;

    /**
     * @var ThemeManager
     */
    private $themeManager;

    /**
     * @param BlockHandlerRegistryInterface $blockRegistry
     * @param ThemeManager $themeManager
     */
    public function __construct(BlockHandlerRegistryInterface $blockRegistry, ThemeManager $themeManager)
    {
        $this->blockRegistry = $blockRegistry;
        $this->themeManager = $themeManager;
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
     * @return string
     */
    public function renderBlock(\Twig_Environment $environment, $block)
    {
        if ($block instanceof BlockInterface) {

            $handler = $this->blockRegistry->getHandler($block->getType());

            if ($handler instanceof BlockHandler) {
                $handler->setTwig($environment);

                $this->themeManager->setActiveTheme('gim'); // @todo

                if ($template = $this->themeManager->locateTemplate('blocks/' . $block->getType() . '/' . $block->getLayout())) {
                    $handler->setTemplate($template);
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
