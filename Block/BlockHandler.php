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

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
abstract class BlockHandler implements BlockHandlerInterface
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
     * @param array $parameters
     * @return string|null
     */
    public function render(array $parameters = [])
    {
        if ($this->getTemplate()) {
            return $this->twig->render($this->getTemplate(), $parameters);
        }
    }
}
