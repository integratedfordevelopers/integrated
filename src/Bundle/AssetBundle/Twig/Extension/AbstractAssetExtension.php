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

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
abstract class AbstractAssetExtension extends \Twig_Extension
{
    /**
     * @var AssetManager
     */
    protected $manager;

    /**
     * @param AssetManager $manager
     */
    public function __construct(AssetManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return [
            new AssetTokenParser($this->getTag(), static::class),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                $this->getTag(),
                [$this, 'render'],
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
        ];
    }

    /**
     * @param \Twig_Environment $environment
     *
     * @return string
     */
    public function render(\Twig_Environment $environment)
    {
        /** @var \Twig_Template $template */
        $template = $environment->loadTemplate($this->getTemplate());

        $html = [];

        foreach ($this->manager->getAssets() as $asset) {
            $block = substr($this->getTag(), 0, -1).($asset->isInline() ? '_inline' : '');

            $html[] = $template->renderBlock($block, [
                $asset->isInline() ? 'asset_content' : 'asset_url' => $asset->getContent(),
            ]);
        }

        return implode("\n", $html);
    }

    /**
     * @return AssetManager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @return string
     */
    abstract protected function getTemplate();

    /**
     * @return string
     */
    abstract protected function getTag();
}
