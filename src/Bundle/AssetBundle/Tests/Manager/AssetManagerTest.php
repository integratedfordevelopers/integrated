<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\AssetBundle\Tests\Manager;

use Integrated\Bundle\AssetBundle\Manager\AssetManager;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class AssetManagerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var AssetManager
     */
    private $manager;

    protected function setUp(): void
    {
        $this->manager = new AssetManager();
    }

    public function testDuplicateFunction()
    {
        $this->manager->add('script.js');
        $this->manager->add('script2.js');
        $this->manager->add('script.js');

        $this->assertCount(2, $this->manager->getAssets());
    }

    public function testInlineFunction()
    {
        $inline = 'html { color: red; }';

        $this->manager->add($inline, true);

        $asset = $this->manager->getAssets()[0];

        $this->assertTrue($asset->isInline());
        $this->assertEquals($inline, $asset->getContent());
    }

    public function testExceptionFunction()
    {
        $this->expectException('\InvalidArgumentException');

        $this->manager->add('script.js', false, 'invalid');
    }

    public function testPrependFunction()
    {
        $this->manager->add('script2.js');
        $this->manager->add('script3.js', false, AssetManager::MODE_APPEND);
        $this->manager->add('script1.js', false, AssetManager::MODE_PREPEND);

        $assets = $this->manager->getAssets();

        $this->assertEquals('script1.js', $assets[0]->getContent());
        $this->assertEquals('script2.js', $assets[1]->getContent());
        $this->assertEquals('script3.js', $assets[2]->getContent());
    }
}
