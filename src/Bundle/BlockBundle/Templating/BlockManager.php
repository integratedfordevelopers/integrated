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
use Integrated\Bundle\BlockBundle\Block\BlockHandler;
use Integrated\Bundle\BlockBundle\Document\Block\Block;
use Integrated\Bundle\ThemeBundle\Templating\ThemeManager;
use Integrated\Common\Block\BlockHandlerInterface;
use Integrated\Common\Block\BlockHandlerRegistryInterface;
use Integrated\Common\Block\BlockInterface;
use Integrated\Common\Content\ContentInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class BlockManager
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
     * @var ContentInterface
     */
    protected $document;

    /**
     * @param BlockHandlerRegistryInterface $blockRegistry
     * @param ThemeManager                  $themeManager
     * @param DocumentManager               $dm
     * @param \Twig_Environment             $twig
     */
    public function __construct(BlockHandlerRegistryInterface $blockRegistry, ThemeManager $themeManager, DocumentManager $dm, \Twig_Environment $twig)
    {
        $this->blockRegistry = $blockRegistry;
        $this->themeManager = $themeManager;
        $this->repository = $dm->getRepository('IntegratedBlockBundle:Block\Block');
        $this->twig = $twig; // @todo templating service (INTEGRATED-443)
    }

    /**
     * @param BlockInterface|string $block
     * @param array                 $options
     *
     * @return null|string
     */
    public function render($block, array $options = [])
    {
        if (\is_string($block)) {
            $block = $this->getBlock($block);
        }

        if ($block instanceof BlockInterface) {
            try {
                if ($block instanceof Block && (!$block->isPublished() || $block->isDisabled())) {
                    return;
                }
            } catch (DocumentNotFoundException $e) {
                return;
            }

            $handler = $this->blockRegistry->getHandler($block->getType());

            if ($handler instanceof BlockHandlerInterface) {
                if ($handler instanceof BlockHandler) {
                    $handler->setTwig($this->twig);

                    if ($this->document instanceof ContentInterface) {
                        $handler->setDocument($this->document);
                    }

                    $handler->configureOptions($resolver = new OptionsResolver());
                    $options = $resolver->resolve($options);

                    if ($template = $this->themeManager->locateTemplate('blocks/'.$block->getType().'/'.$block->getLayout())) {
                        $handler->setTemplate($template);
                    }
                }

                return $handler->execute($block, $options);
            }
        }
    }

    /**
     * @param string $id
     *
     * @return Block|null
     */
    public function getBlock($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param ContentInterface $document
     *
     * @return $this
     */
    public function setDocument(ContentInterface $document)
    {
        $this->document = $document;

        return $this;
    }
}
