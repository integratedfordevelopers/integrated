<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\BlockBundle\Templating;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use Doctrine\ODM\MongoDB\DocumentRepository;

use Integrated\Common\Block\BlockHandlerInterface;
use Integrated\Common\Block\BlockHandlerRegistryInterface;
use Integrated\Common\Block\BlockInterface;
use Integrated\Bundle\BlockBundle\Block\BlockHandler;
use Integrated\Bundle\ThemeBundle\Templating\ThemeManager;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class BlockRenderer
{
    /**
     * @var BlockHandlerRegistryInterface
     */
    protected $blockRegistry;

    /**
     * @var ThemeManager
     */
    protected $themeManager;

    /**
     * @var DocumentRepository
     */
    protected $repository;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @param BlockHandlerRegistryInterface $blockRegistry
     * @param ThemeManager $themeManager
     * @param DocumentManager $dm
     * @param \Twig_Environment $twig
     */
    public function __construct(BlockHandlerRegistryInterface $blockRegistry, ThemeManager $themeManager, DocumentManager $dm, \Twig_Environment $twig)
    {
        $this->blockRegistry = $blockRegistry;
        $this->themeManager = $themeManager;
        $this->repository = $dm->getRepository('IntegratedBlockBundle:Block\Block');
        $this->twig = $twig; // @todo templating service (INTEGRATED-443)

        $this->themeManager->setActiveTheme('gim'); // @todo (INTEGRATED-385)
    }

    /**
     * @param BlockInterface|string $block
     *
     * @return null|string
     */
    public function render($block)
    {
        if (is_string($block)) {
            $block = $this->repository->find($block);
        }

        if ($block instanceof BlockInterface) {
            try {
                $handler = $this->blockRegistry->getHandler($block->getType());

            } catch (DocumentNotFoundException $e) {
                // @todo log errors (INTEGRATED-444)
                return;
            }

            if ($handler instanceof BlockHandlerInterface) {
                if ($handler instanceof BlockHandler) {
                    $handler->setTwig($this->twig);

                    if ($template = $this->themeManager->locateTemplate('blocks/' . $block->getType() . '/' . $block->getLayout())) {
                        $handler->setTemplate($template);
                    }
                }

                return $handler->execute($block);
            }
        }
    }
}
