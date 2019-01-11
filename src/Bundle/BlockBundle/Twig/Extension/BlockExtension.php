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
use Integrated\Bundle\BlockBundle\Provider\BlockUsageProvider;
use Integrated\Common\Block\BlockInterface;
use Integrated\Common\Form\Mapping\MetadataFactoryInterface;
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

    /**
     * @var BlockUsageProvider
     */
    protected $blockUsageProvider;

    /**
     * @var MetadataFactoryInterface
     */
    protected $metadataFactory;

    /**
     * @var bool
     */
    private $pageBundleInstalled;

    /**
     * @var array
     */
    protected $pages = [];

    /**
     * @param ContainerInterface       $container
     * @param BlockUsageProvider       $blockUsageProvider
     * @param MetadataFactoryInterface $metadataFactory
     */
    public function __construct(
        ContainerInterface $container,
        BlockUsageProvider $blockUsageProvider,
        MetadataFactoryInterface $metadataFactory
    ) {
        $this->container = $container; // @todo remove service container (INTEGRATED-445)
        $this->blockUsageProvider = $blockUsageProvider;
        $this->metadataFactory = $metadataFactory;
        $this->pageBundleInstalled = isset($container->getParameter('kernel.bundles')['IntegratedPageBundle']);
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'integrated_block',
                [$this, 'renderBlock'],
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
            new \Twig_SimpleFunction('integrated_find_channels', [$this, 'findChannels']),
            new \Twig_SimpleFunction('integrated_find_pages', [$this, 'findPages']),
            new \Twig_SimpleFunction('integrated_find_block_types', [$this, 'findBlockTypes']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('integrated_block_type', [$this, 'getBlockTypeName']),
        ];
    }

    /**
     * @param \Twig_Environment                              $environment
     * @param \Integrated\Common\Block\BlockInterface|string $block
     * @param array                                          $options
     *
     * @return string|null
     *
     * @throws \Exception
     */
    public function renderBlock(\Twig_Environment $environment, $block, array $options = [])
    {
        if ($block instanceof BlockInterface) {
            $id = $block->getId();
        } else {
            $id = $block;
            $block = $this->container->get('integrated_block.templating.block_manager')->getBlock($id);
        }

        try {
            // fatal errors are not catched
            $html = $this->container->get('integrated_block.templating.block_manager')->render($block, $options);

            if (!$html) {
                return $environment->render($this->locateTemplate('blocks/empty.html.twig'), [
                    'id' => $id,
                    'block' => $block,
                ]);
            }

            return $html;
        } catch (\Exception $e) {
            if ('prod' !== $this->container->getParameter('kernel.environment')) {
                throw $e;
            }
            $this->container->get('logger')->error(sprintf('Block "%s" contains an error', $id));

            return $environment->render($this->locateTemplate('blocks/error.html.twig'), [
                'id' => $id,
                'block' => $block,
            ]);
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

        if ($this->pageBundleInstalled) {
            /* Get all pages which was associated with current Block document */
            $pages = $this->findPages($block);

            foreach ($pages as $page) {
                if (array_key_exists('channel', $page)) {
                    $channels[$page['channel']['$id']] = $this->blockUsageProvider->getChannel($page['channel']['$id']);
                }
            }
        }

        return $channels;
    }

    /**
     * @param \Integrated\Common\Block\BlockInterface $block
     *
     * @return array
     */
    public function findPages(BlockInterface $block)
    {
        if ($this->pageBundleInstalled) {
            return $this->blockUsageProvider->getPagesPerBlock($block->getId());
        }

        return [];
    }

    /**
     * @param BlockInterface $block
     *
     * @return string
     */
    public function getBlockTypeName(BlockInterface $block)
    {
        return $this->metadataFactory->getMetadata(\get_class($block))->getType();
    }

    /**
     * @return array
     */
    public function findBlockTypes()
    {
        return $this->metadataFactory->getAllMetadata();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_block_block';
    }
}
