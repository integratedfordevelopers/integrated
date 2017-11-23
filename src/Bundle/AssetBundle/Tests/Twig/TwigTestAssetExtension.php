<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\AssetBundle\Tests\Twig;

class TwigTestAssetExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('asset', [$this, 'asset'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function asset($path)
    {
        return '/'.$path;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'asset_extension';
    }
}
