<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\DataFixtures\MongoDB;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;

use Integrated\Bundle\ContentBundle\DataFixtures\MongoDB\Extension\ClassFieldsExtension;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Storage;

use Integrated\Bundle\StorageBundle\DataFixtures\MongoDB\Faker\StorageTrait;

use Nelmio\Alice\Fixtures;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class LoadFixtureData extends ContainerAware implements FixtureInterface
{
    use StorageTrait;
    use ClassFieldsExtension;

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        $files = array();
        $finder =
            Finder::create()
                ->in(__DIR__ . DIRECTORY_SEPARATOR . 'alice')
                ->name('*.yml')
            ->sort(
                function (SplFileInfo $a, SplFileInfo $b) {
                    return (intval($a->getFilename()) < intval($b->getFilename()) ? -1 : 1);
                }
            )
        ;

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($finder as $file) {
            $files[] = $file->getRealpath();
        }

        Fixtures::load($files, $manager, ['providers' => [$this]]);
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
}
