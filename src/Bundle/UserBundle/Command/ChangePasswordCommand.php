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
use Integrated\Bundle\UserBundle\Doctrine\ScopeManager;
use Integrated\Bundle\UserBundle\Doctrine\UserManager;
use Integrated\Bundle\UserBundle\Model\Scope;
use Integrated\Bundle\UserBundle\Model\UserInterface;
use Integrated\Bundle\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\LegacyPasswordHasherInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ChangePasswordCommand extends Command
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var ScopeManager
     */
    private $scopeManager;

    /**
     * @var PasswordHasherFactoryInterface
     */
    private $hasherFactory;

    /**
     * @param ScopeManager                   $scopeManager
     * @param UserManagerInterface           $userManager
     * @param PasswordHasherFactoryInterface $hasherFactory
     */
    public function __construct(
        ScopeManager $scopeManager,
        UserManagerInterface $userManager,
        PasswordHasherFactoryInterface $hasherFactory
    ) {
        $this->scopeManager = $scopeManager;
        $this->userManager = $userManager;
        $this->hasherFactory = $hasherFactory;

        parent::__construct();
    }

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
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username = $input->getArgument('username'); // @todo validate input
        $password = $input->getArgument('password'); // @todo validate input

        $scopeName = 'Integrated';
        if ($input->hasArgument('scope')) {
            $scopeName = $input->getArgument('scope') ?: $scopeName;
        }

        if (!$scope = $this->scopeManager->findByName($scopeName)) {
            $output->writeln(sprintf('Aborting: scope with name "%s" does not exist', $scopeName));

            return 1;
        }

        $user = $this->findUserByScope($username, $scope);

        if (!$user) {
            $output->writeln(sprintf('Aborting: user with username "%s" does not exist', $username));

            return 1;
        }

        $hasher = $this->hasherFactory->getPasswordHasher($user);

        if (!$hasher instanceof LegacyPasswordHasherInterface) {
            $user->setPassword($hasher->hash($password, $user->getSalt()));
            $user->setSalt(null);
        } else {
            $salt = base64_encode(random_bytes(72));

            $user->setPassword($hasher->hash($password, $salt));
            $user->setSalt($salt);
        }

        try {
            $this->userManager->persist($user);
        } catch (Exception $e) {
            $output->writeln(sprintf('Aborting: %s', $e->getMessage()));

            return 1;
        }

        return 0;
    }

    /**
     * @param string $username
     * @param Scope  $scope
     *
     * @return UserInterface|null
     *
     * @throws Exception
     */
    protected function findUserByScope($username, Scope $scope)
    {
        $manager = $this->userManager;
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
                'scope' => (int) $scope->getId(),
            ])
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
