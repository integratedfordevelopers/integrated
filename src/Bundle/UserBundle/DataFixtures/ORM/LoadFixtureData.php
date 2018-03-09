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

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Integrated\Bundle\UserBundle\Model\User;
use Nelmio\Alice\Fixtures;
use Nelmio\Alice\Loader\NativeLoader;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Finder\Finder;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class LoadFixtureData implements ContainerAwareInterface, FixtureInterface
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
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $files = [];

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach (Finder::create()->in($this->path.DIRECTORY_SEPARATOR.'alice')->name('*.yml')->sortByName() as $file) {
            $files[] = $file->getRealpath();
        }

        $loader = new NativeLoader();
        $objectSet = $loader->loadFiles($files, ['locale' => $this->locale]);

        foreach ($objectSet->getObjects() as $object) {
            if ($object instanceof User) {
                $salt = base64_encode(random_bytes(72));

                $object->setSalt($salt);
                $object->setPassword($this->generatePassword($object, $object->getPassword()));
            }
            $manager->persist($object);
        }

        $manager->flush();
    }

    /**
     * @param User $user
     * @param $password
     *
     * @return mixed
     */
    public function generatePassword(User $user, $password)
    {
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);

        return $encoder->encodePassword($password, $user->getSalt());
    }
}
