<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Integrated\Bundle\ContentBundle\DataFixtures\MongoDB\Extension\ArrayCollectionExtension;
use Integrated\Bundle\ContentBundle\DataFixtures\MongoDB\Extension\ClassFieldsExtension;
use Nelmio\Alice\Fixtures;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class LoadFixtureData implements FixtureInterface, ContainerAwareInterface
{
    use ArrayCollectionExtension;
    use ClassFieldsExtension;
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
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $files = [];

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach (Finder::create()->in($this->path.DIRECTORY_SEPARATOR.'alice')->name('*.yml')->sortByName() as $file) {
            $files[] = $file->getRealpath();
        }

        Fixtures::load($files, $manager, ['providers' => [$this], 'locale' => $this->locale]);
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
}
