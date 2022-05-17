<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Nelmio\Alice\Loader\SimpleFilesLoader;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Finder\Finder;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class LoadFixtureData implements ContainerAwareInterface, ORMFixtureInterface
{
    use ContainerAwareTrait;

    /**
     * @var string
     */
    protected $path = __DIR__;

    /**
     * @var string
     */
    protected $locale = 'en_US';

    /**
     * @var SimpleFilesLoader
     */
    private $loader;

    public function __construct(SimpleFilesLoader $loader)
    {
        $this->loader = $loader;
    }

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

        foreach ($this->loader->loadFiles($files)->getObjects() as $object) {
            $manager->persist($object);
        }

        $manager->flush();
    }
}
