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

use Exception;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Integrated\Bundle\UserBundle\Model\Scope;
use Integrated\Bundle\UserBundle\Model\UserManagerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class CreateUserCommand extends ContainerAwareCommand
{
    /**
     * @var UserManagerInterface
     */
    private $manager;

    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('user:create')

            ->addArgument('username', InputArgument::REQUIRED, 'The username')
            ->addArgument('password', InputArgument::REQUIRED, 'The password')
            ->addArgument('scope', InputArgument::OPTIONAL, 'The scope')
            ->addArgument('roles', InputArgument::OPTIONAL, 'Roles')
            ->addArgument('email', InputArgument::OPTIONAL, 'The email address')

            ->setDescription('Create a user')
            ->setHelp('
The <info>%command.name%</info> command creates a new user

<info>php %command.full_name%</info>
');
    }

    /**
     * @see Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        $email = null;

        if ($input->hasArgument('email')) {
            $email = $input->getArgument('email');
        }

        $roles = null;
        if ($input->hasArgument('roles')) {
            $roles = array_filter(explode(',', $input->getArgument('roles')));
        }

        $manager = $this->getManager();

        $user = $manager->create();

        $salt = base64_encode(random_bytes(72));

        $user->setUsername($username);
        $user->setPassword($this->getEncoder($user)->encodePassword($password, $salt));
        $user->setSalt($salt);
        $user->setEmail($email);

        $scopeName = 'Integrated';
        if ($input->hasArgument('scope')) {
            $scopeName = $input->getArgument('scope') ?: $scopeName;
        }

        $scopeManager = $this->getContainer()->get('integrated_user.scope.manager');
        if (!$scope = $scopeManager->findByName($scopeName)) {
            $scope = new Scope();
            $scope
                ->setName($scopeName)
                ->setAdmin($scopeName == 'Integrated')
            ;

            $scopeManager->persist($scope, true);
        }

        $user->setScope($scope);

        $validator = $this->getContainer()->get('validator');
        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $output->writeln(sprintf('Aborting: user model not valid: %s', (string) $errors));

            return 1;
        }

        if ($roles) {
            $roleManager = $this->getContainer()->get('integrated_user.role.manager');
            $roleRepository = $roleManager->getRepository();
            $allRoles = $roleManager->getRolesFromSources();

            foreach ($roles as $role) {
                if ($objectRole = $roleRepository->findOneBy(['role' => $role])) {
                    $user->addRole($objectRole);
                } elseif (isset($allRoles[$role])) {
                    $objectRole = $roleManager->create($role);
                    $roleManager->persist($objectRole);
                    $user->addRole($objectRole);
                } else {
                    $output->writeln(sprintf('The role %s not found ', $role));
                }
            }
        }

        try {
            $manager->persist($user);
        } catch (Exception $e) {
            $output->writeln(sprintf('Aborting: %s', $e->getMessage()));

            return 1;
        }

        return 0;
    }

    /**
     * @param object $user
     *
     * @return PasswordEncoderInterface
     */
    protected function getEncoder($user)
    {
        if ($this->encoderFactory === null) {
            $this->encoderFactory = $this->getContainer()->get('security.encoder_factory');
        }

        return $this->encoderFactory->getEncoder($user);
    }

    /**
     * @return UserManagerInterface
     */
    protected function getManager()
    {
        if ($this->manager === null) {
            $this->manager = $this->getContainer()->get('integrated_user.user.manager');
        }

        return $this->manager;
    }
}
