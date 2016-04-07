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

use Symfony\Component\DependencyInjection\ContainerInterface;

use Integrated\Bundle\BlockBundle\Document\Block\Block;
use Integrated\Common\Block\BlockInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class BlockExtension extends \Twig_Extension
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
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
     * @param array $options
     *
     * @return null|string
     *
     * @throws \Exception
     */
    public function renderBlock(\Twig_Environment $environment, $block, array $options = [])
    {
        $id = $block instanceof Block ? $block->getId() : $block;

        try {
            // fatal errors are not catched
            $html = $this->container->get('integrated_block.templating.block_manager')->render($block, $options);

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
     * @return \Integrated\Bundle\ContentBundle\Document\Channel\Channel[]
     */
    public function findChannels(BlockInterface $block)
    {
        $channels = [];

        if ($this->container->has('integrated_page.form.type.page')) {
            /* Get all pages which was associated with current Block document */
            $pages = $this->getPages($block);

            foreach ($pages as $page) {
                if ($channel = $page->getChannel()) {
                    $channels[$channel->getId()] = $channel;
                }
            }
        }

        return $channels;
    }

    /**
     * @param \Integrated\Common\Block\BlockInterface $block
     *
     * @return \Integrated\Bundle\PageBundle\Document\Page\Page[]
     */
    public function findPages(BlockInterface $block)
    {
        $pages = [];

        if ($this->container->has('integrated_page.form.type.page')) {
            foreach ($this->getPages($block) as $page) {
                $pages[$page->getId()] = $page;
            }
        }

        return $pages;
    }

    /**
     * @param Block $block
     * @return \Integrated\Bundle\PageBundle\Document\Page\Page[]
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
