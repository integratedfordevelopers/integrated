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
use Integrated\Bundle\UserBundle\Doctrine\RoleManager;
use Integrated\Bundle\UserBundle\Doctrine\ScopeManager;
use Integrated\Bundle\UserBundle\Model\Scope;
use Integrated\Bundle\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class CreateUserCommand extends Command
{
    /**
     * @var ScopeManager
     */
    private $scopeManager;

    /**
     * @var RoleManager
     */
    private $roleManager;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * @param ScopeManager            $scopeManager
     * @param RoleManager             $roleManager
     * @param UserManagerInterface    $userManager
     * @param ValidatorInterface      $validator
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(
        ScopeManager $scopeManager,
        RoleManager $roleManager,
        UserManagerInterface $userManager,
        ValidatorInterface $validator,
        EncoderFactoryInterface $encoderFactory
    ) {
        $this->scopeManager = $scopeManager;
        $this->roleManager = $roleManager;
        $this->userManager = $userManager;
        $this->validator = $validator;
        $this->encoderFactory = $encoderFactory;

        parent::__construct();
    }

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
    protected function execute(InputInterface $input, OutputInterface $output): int
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

        $user = $this->userManager->create();

        $salt = base64_encode(random_bytes(72));

        $user->setUsername($username);
        $user->setPassword($this->encoderFactory->getEncoder($user)->encodePassword($password, $salt));
        $user->setSalt($salt);
        $user->setEmail($email);

        $scopeName = 'Integrated';
        if ($input->hasArgument('scope')) {
            $scopeName = $input->getArgument('scope') ?: $scopeName;
        }

        if (!$scope = $this->scopeManager->findByName($scopeName)) {
            $scope = new Scope();
            $scope
                ->setName($scopeName)
                ->setAdmin($scopeName == 'Integrated')
            ;

            $this->scopeManager->persist($scope, true);
        }

        $user->setScope($scope);

        $errors = $this->validator->validate($user);

        if (\count($errors) > 0) {
            $output->writeln(sprintf('Aborting: user model not valid: %s', (string) $errors));

            return 1;
        }

        if ($roles) {
            $roleRepository = $this->roleManager->getRepository();
            $allRoles = $this->roleManager->getRolesFromSources();

            foreach ($roles as $role) {
                if ($objectRole = $roleRepository->findOneBy(['role' => $role])) {
                    $user->addRole($objectRole);
                } elseif (isset($allRoles[$role])) {
                    $objectRole = $this->roleManager->create($role);
                    $this->roleManager->persist($objectRole);
                    $user->addRole($objectRole);
                } else {
                    $output->writeln(sprintf('The role %s not found ', $role));
                }
            }
        }

        try {
            $this->userManager->persist($user);
        } catch (Exception $e) {
            $output->writeln(sprintf('Aborting: %s', $e->getMessage()));

            return 1;
        }

        return 0;
    }
}
