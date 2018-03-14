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

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory as FakerGeneratorFactory;
use Integrated\Bundle\ContentBundle\DataFixtures\Faker\Provider\ContentTypeProvider;
use Integrated\Bundle\StorageBundle\DataFixtures\Faker\Provider\ImageProvider;
use Integrated\Bundle\StorageBundle\DataFixtures\Faker\Provider\VideoProvider;
use Nelmio\Alice\Faker\Provider\AliceProvider;
use Nelmio\Alice\Loader\NativeLoader;
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

    /**
     * @var string
     */
    protected $locale = 'en_US';

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $finder = Finder::create()
            ->in(__DIR__.DIRECTORY_SEPARATOR.'alice')
            ->name('*.yml')
            ->sort(
                function (SplFileInfo $a, SplFileInfo $b) {
                    return (int) ($a->getFilename()) < (int) ($b->getFilename()) ? -1 : 1;
                }
            );

        $files = [];

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($finder as $file) {
            $files[] = $file->getRealpath();
        }

        $generator = FakerGeneratorFactory::create($this->locale);
        $generator->addProvider(new AliceProvider());

        // add Integrated custom providers
        $generator->addProvider(new ImageProvider($this->getContainer()->get('integrated_storage.manager')));
        $generator->addProvider(new VideoProvider($this->getContainer()->get('integrated_storage.manager')));
        $generator->addProvider(new ContentTypeProvider($manager));

        $loader = new NativeLoader($generator);
        $objectSet = $loader->loadFiles($files);

        foreach ($objectSet->getObjects() as $object) {
            $manager->persist($object);
        }

        $manager->flush();
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
}
