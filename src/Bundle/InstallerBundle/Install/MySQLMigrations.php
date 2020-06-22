<?php

namespace Integrated\Bundle\InstallerBundle\Install;

use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Exception\UnknownMigrationVersion;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MySQLMigrations
{
    const DOCTRINE_MIGRATIONS_DIRECTORY = '/../Migrations/MySQL';
    const DOCTRINE_MIGRATIONS_NAMESPACE = 'Integrated\Bundle\InstallerBundle\Migrations\MySQL';
    const DOCTRINE_MIGRATIONS_NAME = 'Integrated MySQL Migrations';
    const DOCTRINE_MIGRATIONS_TABLE = 'integrated_migration_versions';
    const DOCTRINE_MIGRATIONS_DIRECTION_UP = 'up';

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Migrations constructor.
     *
     * @param EntityManager      $entityManager
     * @param ContainerInterface $container
     */
    public function __construct(EntityManager $entityManager, ContainerInterface $container)
    {
        $this->entityManager = $entityManager;
        $this->container = $container;
    }

    public function execute()
    {
        $directory = realpath(__DIR__.self::DOCTRINE_MIGRATIONS_DIRECTORY);

        $configuration = new Configuration($this->entityManager->getConnection());
        $configuration->setMigrationsNamespace(self::DOCTRINE_MIGRATIONS_NAMESPACE);
        $configuration->setMigrationsDirectory($directory);
        $configuration->registerMigrationsFromDirectory($directory);
        $configuration->setName(self::DOCTRINE_MIGRATIONS_NAME);
        $configuration->setMigrationsTableName(self::DOCTRINE_MIGRATIONS_TABLE);

        $to = $configuration->getLatestVersion();
        $versions = $configuration->getMigrationsToExecute(self::DOCTRINE_MIGRATIONS_DIRECTION_UP, $to);
        foreach ($versions as $version) {
            $migration = $version->getMigration();
            if ($migration instanceof ContainerAwareInterface) {
                $migration->setContainer($this->container);
            }
            $version->execute(self::DOCTRINE_MIGRATIONS_DIRECTION_UP);
        }
    }
}
