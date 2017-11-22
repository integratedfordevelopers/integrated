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

use Symfony\Component\OptionsResolver\OptionsResolver;

use Integrated\Common\Block\BlockHandlerInterface;
use Integrated\Common\Block\BlockInterface;
use Integrated\Common\Content\ContentInterface;

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
     * @param \Twig_Environment $twig
     * @return $this
     */
    public function setTwig(\Twig_Environment $twig)
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
     * @return $this
     */
    public function setDocument(ContentInterface $document)
    {
        $this->document = $document;
        return $this;
    }

    /**
     * @param array $parameters
     * @return string|null
     */
    public function render(array $parameters = [])
    {
        if ($this->getTemplate()) {
            return $this->twig->render($this->getTemplate(), $parameters);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockInterface $block, array $options)
    {
        return $this->render([
            'block'    => $block,
            'document' => $this->getDocument(),
        ]);
    }

    /**
     * Configures the options for this block handler.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
