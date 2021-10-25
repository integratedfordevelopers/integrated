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

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Loader\SimpleFilesLoader;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Finder\Finder;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class LoadFixtureData implements ContainerAwareInterface, FixtureInterface
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $files = [];

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach (Finder::create()->in(__DIR__.\DIRECTORY_SEPARATOR.'alice')->name('*.yml')->sortByName() as $file) {
            $files[] = $file->getRealpath();
        }

        foreach ($this->getLoader()->loadFiles($files)->getObjects() as $object) {
            $manager->persist($object);
        }

        $manager->flush();
    }

    /**
     * @return SimpleFilesLoader
     */
    private function getLoader()
    {
        return $this->container->get('nelmio_alice.files_loader.simple');
    }
}
