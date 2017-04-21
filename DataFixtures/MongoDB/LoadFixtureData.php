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

use Integrated\Bundle\ContentBundle\DataFixtures\MongoDB\Extension\ContentTypeExtension;
use Integrated\Bundle\StorageBundle\DataFixtures\MongoDB\Extension\FileExtensionTrait;
use Integrated\Bundle\StorageBundle\DataFixtures\MongoDB\Extension\ImageExtensionTrait;

use Nelmio\Alice\Fixtures;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class LoadFixtureData implements FixtureInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;
    use FileExtensionTrait;
    use ImageExtensionTrait;
    use ClassFieldsExtension;
    use ContentTypeExtension;

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        $finder = Finder::create()
            ->in(__DIR__ . DIRECTORY_SEPARATOR . 'alice')
            ->name('*.yml')
            ->sort(
                function (SplFileInfo $a, SplFileInfo $b) {
                    return (intval($a->getFilename()) < intval($b->getFilename()) ? -1 : 1);
                }
            );

        $files = [];

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
