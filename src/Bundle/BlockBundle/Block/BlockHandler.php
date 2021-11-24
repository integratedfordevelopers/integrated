<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\BlockBundle\Block;

use Integrated\Common\Block\BlockHandlerInterface;
use Integrated\Common\Block\BlockInterface;
use Integrated\Common\Block\BlockRequiredItemsInterface;
use Integrated\Common\Content\ContentInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class BlockHandler implements BlockHandlerInterface
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var string
     */
    private $template;

    /**
     * @var ContentInterface
     */
    private $document;

    /**
     * @param Environment $twig
     *
     * @return $this
     */
    public function setTwig(Environment $twig)
    {
        $this->twig = $twig;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template
     *
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get current document which can be used in related blocks.
     *
     * @return ContentInterface|null
     */
    public function getDocument()
    {
        return $this->document;
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

    /**
     * @param array $parameters
     *
     * @return string|null
     */
    public function render(array $parameters = [])
    {
        if ($this->getTemplate()) {
            return $this->twig->render($this->getTemplate(), $parameters);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockInterface $block, array $options)
    {
        if (!$this->isAllowed($block)) {
            return '';
        }

        return $this->render([
            'block' => $block,
            'document' => $this->getDocument(),
            'options' => $options,
        ]);
    }

    /**
     * Configures the options for this block handler.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'gridLevel' => 0,
        ]);
    }

    /**
     * @param BlockInterface $block
     *
     * @return bool
     */
    private function isAllowed(BlockInterface $block)
    {
        if (!$block instanceof BlockRequiredItemsInterface) {
            return true;
        }

        if (!$relation = $block->getRequiredRelation()) {
            return true;
        }

        if (\count($block->getRequiredItems()) == 0) {
            return true;
        }

        if (!$this->getDocument() instanceof ContentInterface) {
            return false;
        }

        if ($relation = $this->getDocument()->getRelation($block->getRequiredRelation()->getId())) {
            foreach ($relation->getReferences() as $reference) {
                foreach ($block->getRequiredItems() as $requiredItem) {
                    if ($requiredItem->getId() == $reference->getId()) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
