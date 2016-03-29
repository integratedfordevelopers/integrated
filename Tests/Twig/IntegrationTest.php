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
use Integrated\Bundle\AssetBundle\Twig\Extension\AssetExtension;

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
            new AssetExtension(new AssetManager(), 'integrated_stylesheets', 'stylesheets.html.twig'),
            new AssetExtension(new AssetManager(), 'integrated_javascripts', 'javascripts.html.twig'),
        ];
    }

    /**
     * @dataProvider getTests
     */
    public function testIntegration($file, $message, $condition, $templates, $exception, $outputs)
    {
        $this->loadTemplate($templates, 'stylesheets.html.twig');
        $this->loadTemplate($templates, 'javascripts.html.twig');

        $this->doIntegrationTest($file, $message, $condition, $templates, $exception, $outputs);
    }

    /**
     * @param array $templates
     * @param string $name
     */
    protected function loadTemplate(&$templates, $name)
    {
        $templates[$name] = file_get_contents(dirname(__FILE__).'/../../Resources/views/Asset/'.$name);
    }

    /**
     * {@inheritdoc}
     */
    protected function getFixturesDir()
    {
        return dirname(__FILE__).'/Fixtures/';
    }
}

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
