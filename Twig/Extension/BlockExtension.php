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

use Integrated\Bundle\BlockBundle\Document\Block\Block;
use Integrated\Common\Block\BlockInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class BlockExtension extends \Twig_Extension
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /** @var array */
    protected $pages = [];

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container; // @todo remove service container (INTEGRATED-445)
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('integrated_block', [$this, 'renderBlock'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new \Twig_SimpleFunction('integrated_find_channels', [$this, 'findChannels']),
            new \Twig_SimpleFunction('integrated_find_pages', [$this, 'findPages']),
        ];
    }

    /**
     * @param \Twig_Environment $environment
     * @param \Integrated\Common\Block\BlockInterface|string $block
     *
     * @return null|string
     *
     * @throws \Exception
     */
    public function renderBlock(\Twig_Environment $environment, $block)
    {
        $id = $block instanceof Block ? $block->getId() : $block;

        try {
            // fatal errors are not catched
            $html = $this->container->get('integrated_block.templating.block_manager')->render($block);

            if (!$html) {
                return $environment->render($this->locateTemplate('blocks/empty.html.twig'), ['id' => $id]);
            }

            return $html;

        } catch (\Exception $e) {
            if ('prod' !== $this->container->getParameter('kernel.environment')) {
                throw $e;
            } else {
                $this->container->get('logger')->error(sprintf('Block "%s" contains an error', $id));
            }

            return $environment->render($this->locateTemplate('blocks/error.html.twig'), ['id' => $id]);
        }
    }

    /**
     * @param string $template
     *
     * @return string
     */
    protected function locateTemplate($template)
    {
        return $this->container->get('integrated_theme.templating.theme_manager')->locateTemplate($template);
    }

    /**
     * @param \Integrated\Common\Block\BlockInterface $block
     *
     * @return null|string
     */
    public function findChannels($block)
    {
        $channelNames = [];
        if ($this->container->has('integrated_page.form.type.page')) {
            /* Get all pages which was associated with current Block document */
            $pages = $this->getPages($block);

            $channelNames = [];
            foreach ($pages as $page) {
                if ($channel = $page->getChannel()) {
                    $channelNames[] = $channel->getName();
                }
            }
        }

        return implode(',', $channelNames);
    }

    /**
     * @param \Integrated\Common\Block\BlockInterface $block
     *
     * @return null|string
     */
    public function findPages($block)
    {
        $pageNames = [];
        if ($this->container->has('integrated_page.form.type.page')) {
            foreach ($this->getPages($block) as $page) {
                $pageNames[] = $page->getTitle();
            }
        }

        return implode(',', $pageNames);
    }

    /**
     * @param Block $block
     * @return mixed
     */
    public function getPages(Block $block)
    {
        if (!isset($this->pages[$block->getId()])) {
            /* Get all pages which was associated with current Block document */
            $this->pages[$block->getId()] = $this->container
                ->get('doctrine_mongodb')
                ->getRepository('IntegratedBlockBundle:Block\Block')
                ->pagesByBlockQb($block)
                ->execute();
        }

        return $this->pages[$block->getId()];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_block_block';
    }
}
