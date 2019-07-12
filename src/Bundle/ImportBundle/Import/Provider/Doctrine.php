<?php

namespace Integrated\Bundle\ImportBundle\Import\Provider;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Integrated\Bundle\ImportBundle\Document\ImportDefinition;

class Doctrine
{
    /**
     * Doctrine constructor.
     */
    public function __construct(
    ) {
    }

    /**
     * @param ImportDefinition $importDefinition
     *
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function toArray(ImportDefinition $importDefinition) {
        $config = new Configuration();

        $connectionParams = array(
            'url' => $importDefinition->getConnectionUrl(),
            'charset' => 'utf8',
        );
        $connection = DriverManager::getConnection($connectionParams, $config);

        $result = $connection->fetchAll($importDefinition->getConnectionQuery());

        //add a heading array
        $startRow = [];
        if (count($result)) {
            foreach ($result[0] as $column => $value) {
                $startRow[] = $column;
            }
        }

        array_unshift($result, $startRow);

        return $result;
    }
}