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
use Nelmio\Alice\Fixtures;
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

        Fixtures::load($files, $manager, ['providers' => [$this], 'locale' => $this->locale]);
    }
}
