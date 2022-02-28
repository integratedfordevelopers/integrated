<?php

namespace Integrated\Bundle\InstallerBundle\Install;

use AntiMattr\MongoDB\Migrations\Configuration\Configuration;
use AntiMattr\MongoDB\Migrations\Migration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MongoDBMigrations
{
    public const DOCTRINE_MIGRATIONS_DIRECTORY = '/../Migrations/MongoDB';
    public const DOCTRINE_MIGRATIONS_NAMESPACE = 'Integrated\Bundle\InstallerBundle\Migrations\MongoDB';
    public const DOCTRINE_MIGRATIONS_NAME = 'Integrated MongoDB Migrations';
    public const DOCTRINE_MIGRATIONS_COLLECTION = 'integrated_migration_versions';
    public const DOCTRINE_MIGRATIONS_DIRECTION_UP = 'up';

    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Migrations constructor.
     *
     * @param DocumentManager    $documentManager
     * @param ContainerInterface $container
     */
    public function __construct(DocumentManager $documentManager, ContainerInterface $container)
    {
        $this->documentManager = $documentManager;
        $this->container = $container;
    }

    /**
     * Execute migrations.
     */
    public function execute()
    {
        $directory = realpath(__DIR__.self::DOCTRINE_MIGRATIONS_DIRECTORY);

        $configuration = new Configuration($this->documentManager->getClient());
        $configuration->setMigrationsCollectionName(self::DOCTRINE_MIGRATIONS_COLLECTION);
        $configuration->setMigrationsDatabaseName($this->documentManager->getDocumentDatabase(Content::class)->getDatabaseName());
        $configuration->setMigrationsDirectory($directory);
        $configuration->setMigrationsNamespace(self::DOCTRINE_MIGRATIONS_NAMESPACE);
        $configuration->setName(self::DOCTRINE_MIGRATIONS_NAME);
        $configuration->registerMigrationsFromDirectory($directory);

        $to = $configuration->getLatestVersion();
        $versions = $configuration->getMigrationsToExecute(self::DOCTRINE_MIGRATIONS_DIRECTION_UP, $to);
        foreach ($versions as $version) {
            $migration = $version->getMigration();
            if ($migration instanceof ContainerAwareInterface) {
                $migration->setContainer($this->container);
            }
        }

        $migration = new Migration($configuration);
        $migration->migrate();
    }
}
