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

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class IntegrationTest extends \Twig_Test_IntegrationTestCase
{
    /**
     * {@inheritdoc}
     */
    public function getExtensions()
    {
        return [
            new TwigTestAssetExtension(),
            new StylesheetExtension(new AssetManager(), 'integrated_stylesheets', 'stylesheets.html.twig'),
            new JavascriptExtension(new AssetManager(), 'integrated_javascripts', 'javascripts.html.twig'),
        ];
    }

    /**
     * @dataProvider getTests
     */
    public function testIntegration($file, $message, $condition, $templates, $exception, $outputs)
    {
        $templates['@IntegratedAssetBundle/Resources/views/Asset/javascripts.html.twig'] = file_get_contents(
            __DIR__.'/../../Resources/views/Asset/javascripts.html.twig'
        );

        $templates['@IntegratedAssetBundle/Resources/views/Asset/stylesheets.html.twig'] = file_get_contents(
            __DIR__.'/../../Resources/views/Asset/stylesheets.html.twig'
        );

        $this->doIntegrationTest($file, $message, $condition, $templates, $exception, $outputs);
    }

    /**
     * {@inheritdoc}
     */
    protected function getFixturesDir()
    {
        return __DIR__.'/Fixtures/';
    }
}
