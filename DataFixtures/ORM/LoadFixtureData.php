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

use Integrated\Bundle\UserBundle\Model\User;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\DependencyInjection\ContainerAware;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;

use Nelmio\Alice\Fixtures;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class LoadFixtureData extends ContainerAware implements FixtureInterface
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
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        $files = array();

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach (Finder::create()->in($this->path . DIRECTORY_SEPARATOR  . 'alice')->name('*.yml')->sortByName() as $file) {
            $files[] = $file->getRealpath();
        }

        Fixtures::load($files, $manager, ['providers' => [$this], 'locale' => $this->locale]);
    }

    /**
     * @return string
     */
    public function generateSalt()
    {
        $generator = $this->container->get('security.secure_random');
        return  base64_encode($generator->nextBytes(72));
    }

    /**
     * @param User $user
     * @param $password
     * @return mixed
     */
    public function generatePassword(User $user, $password)
    {
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        return $encoder->encodePassword($password, $user->getSalt());
    }
}