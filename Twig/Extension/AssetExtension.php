<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\AssetBundle\Twig\Extension;

use Integrated\Bundle\AssetBundle\Manager\AssetManager;
use Integrated\Bundle\AssetBundle\Twig\TokenParser\AssetTokenParser;

use Doctrine\Common\Inflector\Inflector;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class AssetExtension extends \Twig_Extension
{
    /**
     * @var AssetManager
     */
    protected $manager;

    /**
     * @var string
     */
    protected $tag;

    /**
     * @var string
     */
    protected $template;

    /**
     * @param AssetManager $manager
     * @param string $tag
     * @param string $template
     */
    public function __construct(AssetManager $manager, $tag, $template)
    {
        $this->manager = $manager;
        $this->tag = $tag;
        $this->template = $template;
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return [
            new AssetTokenParser($this->tag),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction($this->tag, [$this, 'render'], ['is_safe' => ['html'], 'needs_environment' => true]),
        ];
    }

    /**
     * @param \Twig_Environment $environment
     * @return string
     */
    public function render(\Twig_Environment $environment)
    {
        /** @var \Twig_Template $template */
        $template = $environment->loadTemplate($this->template);

        $html = [];

        foreach ($this->manager->getAssets() as $asset) {
            $block = Inflector::singularize($this->tag) . ($asset->isInline() ? '_inline' : '');

            $html[] = $template->renderBlock($block, [
                $asset->isInline() ? 'asset_content' : 'asset_url' => $asset->getContent()
            ]);
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @return AssetManager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->tag . '_extension';
    }
}
