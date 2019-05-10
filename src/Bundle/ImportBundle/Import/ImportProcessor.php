<?php

namespace Integrated\Bundle\ImportBundle\Import;

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\StorageBundle\Storage\Cache\AppCache;

class ImportProcessor
{
    protected $storageCache;

    protected $documentManager;

    /**
     * ImportFile constructor.
     *
     * @param AppCache        $storageCache
     * @param DocumentManager $documentManager
     */
    public function __construct(
        AppCache $storageCache,
        DocumentManager $documentManager
    ) {
        $this->storageCache = $storageCache;
        $this->documentManager = $documentManager;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        $fields = ['' => ['label' => '- ignore -', 'matchCol' => false]];

        return $fields;
    }

}
