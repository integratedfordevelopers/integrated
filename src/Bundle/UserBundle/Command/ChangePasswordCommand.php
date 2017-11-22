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

use Integrated\Bundle\UserBundle\Doctrine\UserManager;
use Integrated\Bundle\UserBundle\Model\Scope;
use Integrated\Bundle\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

use Integrated\Bundle\UserBundle\Model\UserManagerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ChangePasswordCommand extends ContainerAwareCommand
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
            ->setName('user:password:change')

            ->addArgument('username', InputArgument::REQUIRED, 'The username')
            ->addArgument('password', InputArgument::REQUIRED, 'The password')
            ->addArgument('scope', InputArgument::OPTIONAL, 'The scope')

            ->setDescription('Change password of a user')
            ->setHelp('
The <info>%command.name%</info> command replaces the password of the user

<info>php %command.full_name%</info>
');
    }

    /**
     * @see Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username'); // @todo validate input
        $password = $input->getArgument('password'); // @todo validate input

        $scopeName = 'Integrated';
        if ($input->hasArgument('scope')) {
            $scopeName = $input->getArgument('scope') ?: $scopeName;
        }

        $scopeManager = $this->getContainer()->get('integrated_user.scope.manager');
        if (!$scope = $scopeManager->findByName($scopeName)) {
            $output->writeln(sprintf('Aborting: scope with name "%s" does not exist', $scopeName));

            return 1;
        }

        $user = $this->findUserByScope($username, $scope);

        if (!$user) {
            $output->writeln(sprintf('Aborting: user with username "%s" does not exist', $username));

            return 1;
        }

        $salt = base64_encode(random_bytes(72));

        $user->setPassword($this->getEncoder($user)->encodePassword($password, $salt));
        $user->setSalt($salt);

        try {
            $this->getManager()->persist($user);
        } catch (Exception $e) {
            $output->writeln(sprintf('Aborting: %s', $e->getMessage()));

            return 1;
        }

        return 0;
    }

    /**
     * @param object $user
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

    /**
     * @param string $username
     * @param Scope $scope
     * @return UserInterface|null
     * @throws Exception
     */
    protected function findUserByScope($username, Scope $scope)
    {
        $manager = $this->getManager();
        if (!$manager instanceof UserManager) {
            throw new \Exception(sprintf('Manager should be instance of %s', UserManager::class));
        }

        return $manager->createQueryBuilder()
            ->select('User')
            ->leftJoin('User.scope', 'Scope')
            ->where('User.username = :username')
            ->andWhere('User.scope = :scope')
            ->setParameters([
                'username' => $username,
                'scope' => (int) $scope->getId()
            ])
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
