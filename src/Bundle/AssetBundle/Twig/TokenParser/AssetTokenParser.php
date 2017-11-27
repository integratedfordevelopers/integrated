<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\AssetBundle\Twig\TokenParser;

use Integrated\Bundle\AssetBundle\Manager\AssetManager;
use Integrated\Bundle\AssetBundle\Twig\Node\AssetNode;
use Twig_Token;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class AssetTokenParser extends \Twig_TokenParser
{
    /**
     * @var string
     */
    private $tag;

    /**
     * @var string
     */
    private $extension;

    /**
     * @param string $tag
     * @param string $extension
     */
    public function __construct($tag, $extension)
    {
        $this->tag = $tag;
        $this->extension = $extension;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(Twig_Token $token)
    {
        $assets = [];
        $inline = false;
        $mode = AssetManager::MODE_APPEND;

        $stream = $this->parser->getStream();

        while (!$stream->test(\Twig_Token::BLOCK_END_TYPE)) {
            if ($stream->test(\Twig_Token::STRING_TYPE)) {
                // 'js/src/extra.js'
                $assets[] = $stream->next()->getValue();
            } elseif ($stream->test(\Twig_Token::NAME_TYPE, 'inline')) {
                // inline=true
                $stream->next();
                $stream->expect(\Twig_Token::OPERATOR_TYPE, '=');
                $inline = 'true' === $stream->expect(\Twig_Token::NAME_TYPE, ['true', 'false'])->getValue();
            } elseif ($stream->test(\Twig_Token::NAME_TYPE, 'mode')) {
                // mode='prepend'
                $stream->next();
                $stream->expect(\Twig_Token::OPERATOR_TYPE, '=');
                $mode = $stream->expect(\Twig_Token::STRING_TYPE)->getValue();
            }
        }

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        $body = $this->parser->subparse([$this, 'testEndTag'], true);

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        return new AssetNode(
            ['body' => $body],
            ['assets' => $assets, 'inline' => $inline, 'mode' => $mode],
            $token->getLine(),
            $this->tag,
            $this->extension
        );
    }

    /**
     * @param Twig_Token $token
     *
     * @return bool
     */
    public function testEndTag(\Twig_Token $token)
    {
        return $token->test(['end'.$this->getTag()]);
    }

    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return $this->tag;
    }
}
