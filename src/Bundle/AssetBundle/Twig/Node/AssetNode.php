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

use Twig\Compiler;
use Twig\Node\Node;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class AssetNode extends Node
{
    /**
     * @var string
     */
    private $extension;

    /**
     * @param array  $nodes
     * @param array  $attributes
     * @param int    $lineno
     * @param string $tag
     * @param string $extension
     */
    public function __construct(array $nodes, array $attributes, $lineno, $tag, $extension)
    {
        $this->extension = $extension;

        parent::__construct($nodes, $attributes, $lineno, $tag);
    }

//    /**
//     * @param \Twig_Node $body
//     * @param array      $assets
//     * @param bool       $inline
//     * @param string     $mode
//     * @param int        $lineno
//     * @param string     $tag
//     */
//    public function __construct(
//        \Twig_Node $body,
//        array $assets = [],
//        $inline = false,
//        $mode = null,
//        $lineno = 0,
//        $tag = null
//    ) {
//        parent::__construct(
//            ['body' => $body],
//            ['assets' => $assets, 'inline' => $inline, 'mode' => $mode],
//            $lineno,
//            $tag
//        );
//    }

    /**
     * {@inheritdoc}
     */
    public function compile(Compiler $compiler)
    {
        $body = $this->getNode('body');

        if ($this->getAttribute('inline')) {
            $this->setAttribute('assets', $body->getAttribute('data'));
        }

        $compiler
            ->addDebugInfo($this)
            ->write('$this->env->getExtension(')
                ->string($this->extension)
            ->write(')->getManager()->add(')
                ->repr($this->getAttribute('assets'))
                ->write(', ')
                ->repr($this->getAttribute('inline'))
                ->write(', ')
                ->repr($this->getAttribute('mode'))
            ->write(');');
    }
}
