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

use Integrated\Common\Block\BlockInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class TextBlockHandler extends BlockHandler
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockInterface $block)
    {
        return $this->twig->render('IntegratedBlockBundle:templates/default:blocks/text/default.html.twig', [
            'block' => $block,
        ]);
    }
}
