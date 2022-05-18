<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WebsiteBundle\DataFixtures\MongoDB;

use Doctrine\Bundle\MongoDBBundle\Fixture\ODMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Nelmio\Alice\Loader\SimpleFilesLoader;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Finder\Finder;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class LoadFixtureData implements ContainerAwareInterface, ODMFixtureInterface
{
    use ContainerAwareTrait;

    /**
     * @var SimpleFilesLoader|null
     */
    private $loader;

    public function __construct(?SimpleFilesLoader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        if (!$this->loader) {
            throw new \Exception('Nelmio\Alice is required to load fixtures');
        }

        $files = [];

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach (Finder::create()->in(__DIR__.\DIRECTORY_SEPARATOR.'alice')->name('*.yml')->sortByName() as $file) {
            $files[] = $file->getRealpath();
        }

        foreach ($this->loader->loadFiles($files)->getObjects() as $object) {
            $manager->persist($object);
        }

        $manager->flush();
    }
}
