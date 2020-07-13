<?php

namespace Integrated\Bundle\InstallerBundle\Doctrine\ODM\Migration;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AntiMattr\MongoDB\Migrations;

abstract class AbstractMigration extends Migrations\AbstractMigration implements ContainerAwareInterface
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return '';
    }

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected function getDocumentManager()
    {
        return $this->container->get('doctrine.odm.mongodb.document_manager');
    }
}
