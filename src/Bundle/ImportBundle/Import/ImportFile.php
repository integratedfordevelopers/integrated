<?php

namespace Integrated\Bundle\ImportBundle\Import;

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Document\Content\File;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Field;
use Integrated\Bundle\ImportBundle\Document\ImportDefinition;
use Integrated\Bundle\StorageBundle\Storage\Cache\AppCache;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportFile
{
    /**
     * @var AppCache
     */
    protected $storageCache;

    /**
     * @var DocumentManager
     */
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
     * @return ContentType
     */
    public function getContentType()
    {
        $contentTypeFile = $this->documentManager->find(
            ContentType::class,
            'import_file'
        );

        if (!$contentTypeFile) {
            $file = new Field();
            $file->setName('file');
            $file->setOptions(['required' => true]);

            $contentTypeFile = new ContentType();
            $contentTypeFile->setId('import_file');
            $contentTypeFile->setName('Import file');
            $contentTypeFile->setClass(File::class);
            $contentTypeFile->setOptions(['channels' => ['disabled' => 2]]);
//            $contentTypeFile->addPermission(); todo: add permissions
            $contentTypeFile->setFields([$file]);
            $this->documentManager->persist($contentTypeFile);
            $this->documentManager->flush();
        }

        return $contentTypeFile;
    }

    /**
     * @param ImportDefinition $importDefinition
     *
     * @return array|mixed|string
     *
     * @throws \Exception
     */
    public function toArray(ImportDefinition $importDefinition)
    {
        $file = null;
        if ($importDefinition->getFileId()) {
            $file = $this->documentManager->find(
                File::class,
                $importDefinition->getFileId()
            );
        }

        if (!$file || !$file->getFile()) {
            throw new \Exception('File not available');
        }

        $mimeType = $file->getFile()->getMetadata()->getMimeType();
        $extension = $file->getFile()->getMetadata()->getExtension();
        $filePath = $this->storageCache->path($file->getFile())->getPathname();

        //sometimes the mimetype is not detected correctly
        switch ($extension) {
            case 'json':
                $mimeType = 'application/json';
                break;
            case 'xml':
                $mimeType = 'application/xml';
                break;
        }

        switch ($mimeType) {
            case 'application/json':
                $data = json_decode(file_get_contents($filePath), true);
                break;
            case 'text/xml':
            case 'application/xml':
                $xmlNode = simplexml_load_file($filePath, 'SimpleXMLElement', LIBXML_NOCDATA);
                $alwaysArrayElements = [];
                $data = $this->xmlToArray($xmlNode, [
                    'alwaysArray' => $alwaysArrayElements,
                    'autoText' => false,
                ]);
                $data = json_encode($data);
                $data = json_decode($data, true);
                break;
            default:
                $spreadsheet = IOFactory::load($filePath);
                $worksheet = $spreadsheet->getActiveSheet();
                $data = $worksheet->toArray();
        }

        if (isset($data['rss']['channel']['item'])) {
            //wordpress
            //todo: move to WP filter
            $data = $data['rss']['channel']['item'];
        }

        $startRow = [];

        //place domain attributes in seperate fields
        //todo: move to WP filter
        $j = 0;
        foreach ($data as $index => $row) {
            foreach ($row as $index2 => $value2) {
                if (\is_array($value2)) {
                    //move single domain attributes to field
                    if (isset($value2['@domain']) && isset($value2['$'])) {
                        $data[$index][$index2.'_'.$value2['@domain']][] = $value2['$'];
                        unset($data[$index][$index2]);
                        continue;
                    }

                    //move multi domain attributes to field
                    $unset = false;
                    foreach ($value2 as $index3 => $value3) {
                        if (isset($value3['@domain']) && isset($value3['$'])) {
                            $data[$index][$index2.'_'.$value3['@domain']][] = $value3['$'];
                            $unset = true;
                        }
                    }

                    //move meta info to field
                    foreach ($value2 as $index3 => $value3) {
                        if (isset($value3['wp:meta_key']) && isset($value3['wp:meta_value']) && isset($value3['wp:meta_key']['$']) && isset($value3['wp:meta_value']['$'])) {
                            $data[$index]['meta'.$value3['wp:meta_key']['$']] = $value3['wp:meta_value']['$'];
                            $unset = true;
                        }
                    }

                    if ($unset) {
                        unset($data[$index][$index2]);
                    }
                }
            }
        }

        foreach ($data as $index => $row) {
            foreach ($row as $index2 => $value2) {
                if (!\in_array($index2, $startRow)) {
                    //make sure all fields are in the startRow
                    $startRow[] = $index2;
                }

                //convert to flat values or array, eliminate attributes
                if (\is_array($value2)) {
                    if (isset($value2['$'])) {
                        $data[$index][$index2] = $value2['$'];
                        continue;
                    }

                    if (\count($value2) == 0) {
                        unset($data[$index][$index2]);
                        continue;
                    }

                    $newValue = false;
                    foreach ($value2 as $index3 => $value3) {
                        if (isset($value3['$'])) {
                            $newValue[] = $value3['$'];
                        }
                    }
                    if ($newValue) {
                        $data[$index][$index2] = $newValue;
                    }
                }
            }
        }

        $newData = [$startRow];
        foreach ($data as $row) {
            $newRow = [];
            foreach ($startRow as $startKey) {
                if (isset($row[$startKey])) {
                    $newRow[$startKey] = $row[$startKey];
                } else {
                    $newRow[$startKey] = '';
                }
            }
            $newData[] = $newRow;
        }
        $data = $newData;

        return $data;
    }

    /**
     * @param SimpleXMLElement $xml
     * @param array            $options
     *
     * @return array
     *
     * @see http://outlandish.com/blog/xml-to-json/
     */
    public function xmlToArray(\SimpleXMLElement $xml, $options = [])
    {
        $defaults = [
            'namespaceSeparator' => ':', //you may want this to be something other than a colon
            'attributePrefix' => '@', //to distinguish between attributes and nodes with the same name
            'alwaysArray' => [], //array of xml tag names which should always become arrays
            'autoArray' => true, //only create arrays for tags which appear more than once
            'textContent' => '$', //key used for the text content of elements
            'autoText' => true, //skip textContent key if node has no attributes or child nodes
            'keySearch' => false, //optional search and replace on tag and attribute names
            'keyReplace' => false, //replace values for above search values (as passed to str_replace())
        ];
        $options = array_merge($defaults, $options);
        $namespaces = $xml->getDocNamespaces();
        $namespaces[''] = null; //add base (empty) namespace

        //get attributes from all namespaces
        $attributesArray = [];
        foreach ($namespaces as $prefix => $namespace) {
            foreach ($xml->attributes($namespace) as $attributeName => $attribute) {
                //replace characters in attribute name
                if ($options['keySearch']) {
                    $attributeName = str_replace($options['keySearch'], $options['keyReplace'], $attributeName);
                }
                $attributeKey = $options['attributePrefix']
                    .($prefix ? $prefix.$options['namespaceSeparator'] : '')
                    .$attributeName;
                $attributesArray[$attributeKey] = (string) $attribute;
            }
        }

        //get child nodes from all namespaces
        $tagsArray = [];
        foreach ($namespaces as $prefix => $namespace) {
            foreach ($xml->children($namespace) as $childXml) {
                //recurse into child nodes
                $childArray = self::xmlToArray($childXml, $options);
                list($childTagName, $childProperties) = each($childArray);

                //replace characters in tag name
                if ($options['keySearch']) {
                    $childTagName = str_replace($options['keySearch'], $options['keyReplace'], $childTagName);
                }
                //add namespace prefix, if any
                if ($prefix) {
                    $childTagName = $prefix.$options['namespaceSeparator'].$childTagName;
                }

                if (!isset($tagsArray[$childTagName])) {
                    //only entry with this key
                    //test if tags of this type should always be arrays, no matter the element count
                    $tagsArray[$childTagName] =
                        \in_array($childTagName, $options['alwaysArray']) || !$options['autoArray']
                            ? [$childProperties] : $childProperties;
                } elseif (\is_array($tagsArray[$childTagName]) && array_keys($tagsArray[$childTagName]) === range(0, \count($tagsArray[$childTagName]) - 1)) {
                    //key already exists and is integer indexed array
                    $tagsArray[$childTagName][] = $childProperties;
                } else {
                    //key exists so convert to integer indexed array with previous value in position 0
                    $tagsArray[$childTagName] = [$tagsArray[$childTagName], $childProperties];
                }
            }
        }

        //get text content of node
        $textContentArray = [];
        $plainText = trim((string) $xml);
        if ($plainText !== '') {
            $textContentArray[$options['textContent']] = $plainText;
        }

        //stick it all together
        $propertiesArray = !$options['autoText'] || $attributesArray || $tagsArray || ($plainText === '')
            ? array_merge($attributesArray, $tagsArray, $textContentArray) : $plainText;

        //return node as array
        return [
            $xml->getName() => $propertiesArray,
        ];
    }
}
