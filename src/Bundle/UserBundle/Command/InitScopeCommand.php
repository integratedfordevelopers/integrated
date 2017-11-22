<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Command;

use Integrated\Bundle\UserBundle\Model\Scope;
use Integrated\Bundle\UserBundle\Model\User;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class InitScopeCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('init:scope')
            ->setDescription('Creates default Integrated scope and assign the scope to all user without a scope')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->getContainer()->get('doctrine')->getManagerForClass(Scope::class);
        $repository = $manager->getRepository(Scope::class);
        if (!$scope = $repository->findOneBy(['admin' => true])) {
            $scope = new Scope();
            $scope
                ->setName('Integrated')
                ->setAdmin(true)
            ;

            $manager->persist($scope);
        }

        $manager = $this->getContainer()->get('doctrine')->getManagerForClass(User::class);
        $repository = $manager->getRepository(User::class);
        /** @var User $user */
        foreach ($repository->findBy(['scope' => null]) as $user) {
            $user->setScope($scope);
        }

        $manager->flush();
    }
}
