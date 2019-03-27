<?php

namespace Integrated\Bundle\InstallerBundle\Install;

use Doctrine\DBAL\Migrations\Migration;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MySQLMigrations
{
    const DOCTRINE_MIGRATIONS_DIRECTORY = '/../Migrations/MySQL';
    const DOCTRINE_MIGRATIONS_NAMESPACE = 'Integrated\Bundle\InstallerBundle\Migrations\MySQL';
    const DOCTRINE_MIGRATIONS_NAME = 'Integrated MySQL Migrations';
    const DOCTRINE_MIGRATIONS_TABLE = 'integrated_migration_versions';

    protected $entityManager;

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

    /**
     * @throws \Doctrine\DBAL\Migrations\MigrationException
     */
    public function execute()
    {
        $container = $this->container;
        $connection = $this->entityManager->getConnection();
        $directory = realpath(__DIR__ . self::DOCTRINE_MIGRATIONS_DIRECTORY);

        $configuration = new Configuration($connection);
        $configuration->setMigrationsNamespace(self::DOCTRINE_MIGRATIONS_NAMESPACE);
        $configuration->setMigrationsDirectory($directory);
        $configuration->registerMigrationsFromDirectory($directory);
        $configuration->setName(self::DOCTRINE_MIGRATIONS_NAME);
        $configuration->setMigrationsTableName(self::DOCTRINE_MIGRATIONS_TABLE);

        $versions = $configuration->getMigrations();
        foreach ($versions as $version) {
            $migration = $version->getMigration();
            if ($migration instanceof ContainerAwareInterface) {
                $migration->setContainer($container);
            }
        }

        $migration = new Migration($configuration);
        $migration->migrate();
    }
}
