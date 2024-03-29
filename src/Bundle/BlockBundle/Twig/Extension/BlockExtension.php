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

use Psr\Log\LoggerInterface;
use Integrated\Bundle\ThemeBundle\Exception\CircularFallbackException;
use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Integrated\Bundle\BlockBundle\Provider\BlockUsageProvider;
use Integrated\Bundle\BlockBundle\Templating\BlockManager;
use Integrated\Bundle\ThemeBundle\Templating\ThemeManager;
use Integrated\Common\Block\BlockInterface;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use Integrated\Common\Form\Mapping\MetadataFactoryInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class BlockExtension extends AbstractExtension
{
    /**
     * @var BlockManager
     */
    private $blockManager;

    /**
     * @var ThemeManager
     */
    private $themeManager;

    /**
     * @var BlockUsageProvider
     */
    protected $blockUsageProvider;

    /**
     * @var MetadataFactoryInterface
     */
    protected $metadataFactory;

    /**
     * @var array
     */
    protected $pages = [];

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $environment;

    /**
     * @param BlockManager             $blockManager
     * @param ThemeManager             $themeManager
     * @param BlockUsageProvider       $blockUsageProvider
     * @param MetadataFactoryInterface $metadataFactory
     * @param ChannelContextInterface  $channelContext
     * @param LoggerInterface          $logger
     * @param string                   $environment
     */
    public function __construct(
        BlockManager $blockManager,
        ThemeManager $themeManager,
        BlockUsageProvider $blockUsageProvider,
        MetadataFactoryInterface $metadataFactory,
        ChannelContextInterface $channelContext,
        LoggerInterface $logger,
        string $environment
    ) {
        $this->blockManager = $blockManager;
        $this->themeManager = $themeManager;
        $this->blockUsageProvider = $blockUsageProvider;
        $this->metadataFactory = $metadataFactory;
        $this->channelContext = $channelContext;
        $this->logger = $logger;
        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'integrated_block',
                [$this, 'renderBlock'],
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
            new TwigFunction(
                'integrated_channel_block',
                [$this, 'renderChannelBlock'],
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
            new TwigFunction('integrated_find_channels', [$this, 'findChannels']),
            new TwigFunction('integrated_find_pages', [$this, 'findPages']),
            new TwigFunction('integrated_find_block_types', [$this, 'findBlockTypes']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('integrated_block_type', [$this, 'getBlockTypeName']),
        ];
    }

    /**
     * @param \Twig_Environment     $environment
     * @param BlockInterface|string $block
     * @param array                 $options
     *
     * @return string|null
     *
     * @throws \Exception
     */
    public function renderBlock(Environment $environment, $block, array $options = [])
    {
        if ($block instanceof BlockInterface) {
            $id = $block->getId();
        } else {
            $id = $block;
            $block = $this->blockManager->getBlock($id);
        }

        try {
            // fatal errors are not catched
            $html = $this->blockManager->render($block, $options);

            if (!$html) {
                return $environment->render($this->themeManager->locateTemplate('blocks/empty.html.twig'), [
                    'id' => $id,
                    'block' => $block,
                ]);
            }

            return $html;
        } catch (\Exception $e) {
            if ('prod' !== $this->environment) {
                throw $e;
            }
            $this->logger->error(sprintf('Block "%s" contains an error', $id));

            return $environment->render($this->themeManager->locateTemplate('blocks/error.html.twig'), [
                'id' => $id,
                'block' => $block,
            ]);
        }
    }

    /**
     * @param \Twig_Environment $environment
     * @param string            $id
     * @param string            $name
     * @param string            $class
     * @param array             $options
     *
     * @return string|null
     *
     * @throws CircularFallbackException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function renderChannelBlock(Environment $environment, string $id, string $name, string $class, array $options = [])
    {
        // postfix with channel
        $id = $id.'_'.$this->channelContext->getChannel()->getId();
        $name = $name.' '.$this->channelContext->getChannel()->getName();

        $block = $this->blockManager->getBlock($id);
        if ($block) {
            return $environment->render($this->themeManager->locateTemplate('blocks/channel.html.twig'), [
                'id' => $id,
                'content' => $this->renderBlock($environment, $block, $options),
            ]);
        }

        return $environment->render($this->themeManager->locateTemplate('blocks/create.html.twig'), [
            'id' => $id,
            'name' => $name,
            'class' => $class,
        ]);
    }

    /**
     * @param BlockInterface $block
     *
     * @return Channel[]
     */
    public function findChannels(BlockInterface $block)
    {
        $channels = [];

        /* Get all pages which was associated with current Block document */
        $pages = $this->findPages($block);

        foreach ($pages as $page) {
            if (\array_key_exists('channel', $page)) {
                $channels[$page['channel']['$id']] = $this->blockUsageProvider->getChannel($page['channel']['$id']);
            }
        }

        return $channels;
    }

    /**
     * @param BlockInterface $block
     *
     * @return array
     */
    public function findPages(BlockInterface $block)
    {
        return $this->blockUsageProvider->getPagesPerBlock($block->getId());
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
        $blocks = $this->metadataFactory->getAllMetadata();

        usort($blocks, function ($a, $b) {
            if ($a->getType() === $b->getType()) {
                return 0;
            }

            return ($a->getType() < $b->getType()) ? -1 : 1;
        });

        ksort($blocks);

        return $blocks;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_block_block';
    }
}
