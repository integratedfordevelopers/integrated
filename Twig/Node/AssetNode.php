<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\AssetBundle\Twig\Node;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class AssetNode extends \Twig_Node
{
    /**
     * @param \Twig_Node $body
     * @param array $assets
     * @param bool $inline
     * @param string $mode
     * @param int $lineno
     * @param string $tag
     */
    public function __construct(\Twig_Node $body, array $assets = [], $inline = false, $mode = null, $lineno = 0, $tag = null)
    {
        parent::__construct(['body' => $body], ['assets' => $assets, 'inline' => $inline, 'mode' => $mode], $lineno, $tag);
    }

    /**
     * {@inheritdoc}
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $body = $this->getNode('body');

        if ($this->getAttribute('inline')) {
            $this->setAttribute('assets', $body->getAttribute('data'));
        }

        $compiler
            ->addDebugInfo($this)
            ->write('$this->env->getExtension(')
                ->string($this->tag . '_extension')
            ->write(')->getManager()->add(')
                ->repr($this->getAttribute('assets'))
                ->write(', ')
                ->repr($this->getAttribute('inline'))
                ->write(', ')
                ->repr($this->getAttribute('mode'))
            ->write(');');
    }
}
