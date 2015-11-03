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
        $functions = [
            new \Twig_SimpleFunction('integrated_block', [$this, 'renderBlock'], ['is_safe' => ['html']]),
        ];

        /* check if IntegratedPageBundle is installed */
        if ($this->container->get('integrated_block.bundle_checker')->checkPageBundle()) {
            $functions[] = new \Twig_SimpleFunction('find_channels', [$this, 'findChannels']);
        }

        return $functions;
    }

    /**
     * @param \Integrated\Common\Block\BlockInterface|string $block
     *
     * @return null|string
     */
    public function renderBlock($block)
    {
        return $this->container->get('integrated_block.templating.block_renderer')->render($block);
    }

    /**
     * @param \Integrated\Common\Block\BlockInterface $block
     *
     * @return null|string
     */
    public function findChannels($block)
    {
        /* Get all pages which was associated with current Block document */
        $pages = $this->container
            ->get('doctrine_mongodb')
            ->getRepository('IntegratedBlockBundle:Block\Block')
            ->pagesByBlockQb($block)
            ->execute();

        $channelNames = [];
        foreach ($pages as $page) {
            if ($channel = $page->getChannel()) {
                $channelNames[] = $channel->getName();
            }
        }

        return implode(',', $channelNames);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_block_block';
    }
}
