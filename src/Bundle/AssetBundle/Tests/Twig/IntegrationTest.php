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

use Integrated\Bundle\AssetBundle\Manager\AssetManager;
use Integrated\Bundle\AssetBundle\Twig\Extension\JavascriptExtension;
use Integrated\Bundle\AssetBundle\Twig\Extension\StylesheetExtension;
use Twig\Test\IntegrationTestCase;
use Twig\TwigFunction;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class IntegrationTest extends IntegrationTestCase
{
    /**
     * {@inheritdoc}
     */
    public function getExtensions()
    {
        return [
            new StylesheetExtension(new AssetManager()),
            new JavascriptExtension(new AssetManager()),
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getTwigFunctions()
    {
        return [
            new TwigFunction('asset', function ($path) {
                return '/'.$path;
            }, ['is_safe' => ['html']]),
        ];
    }

    /**
     * @dataProvider getTests
     */
    public function testIntegration($file, $message, $condition, $templates, $exception, $outputs, $deprecation = '')
    {
        $templates = $templates + [
            '@IntegratedAssetBundle/Resources/views/asset/javascripts.html.twig' => file_get_contents(
                __DIR__.'/../../Resources/views/asset/javascripts.html.twig'
            ),
            '@IntegratedAssetBundle/Resources/views/asset/stylesheets.html.twig' => file_get_contents(
                __DIR__.'/../../Resources/views/asset/stylesheets.html.twig'
            ),
        ];

        $this->doIntegrationTest($file, $message, $condition, $templates, $exception, $outputs, $deprecation);
    }

    /**
     * {@inheritdoc}
     */
    protected function getFixturesDir()
    {
        return __DIR__.'/Fixtures/';
    }
}
