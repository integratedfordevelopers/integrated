<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WebsiteBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Integrated\Common\Channel\Connector\Config\Options;
use Nelmio\Alice\Fixtures;
use Nelmio\Alice\Loader\NativeLoader;
use Symfony\Component\Finder\Finder;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class LoadFixtureData implements FixtureInterface
{
    /**
     * @var string
     */
    protected $path = __DIR__;

    /**
     * @var string
     */
    protected $locale = 'en_US';

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $files = [];

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach (Finder::create()->in(
            $this->path.DIRECTORY_SEPARATOR.'alice'
        )->name('*.yml')->sortByName() as $file) {
            $files[] = $file->getRealpath();
        }

        $loader = new NativeLoader();
        $objectSet = $loader->loadFiles($files, ['locale' => $this->locale]);

        foreach ($objectSet->getObjects() as $object) {
            if ($object instanceof Options) {
                // can't be persisted
                continue;
            }
            $manager->persist($object);
        }

        $manager->flush();
    }
}
