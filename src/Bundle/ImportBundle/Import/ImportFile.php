<?php

namespace Integrated\Bundle\ImportBundle\Import;

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Doctrine\ContentTypeManager;
use Integrated\Bundle\ContentBundle\Document\Content\File;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Field;
use Integrated\Bundle\ImportBundle\Document\ImportDefinition;
use Integrated\Bundle\ImportBundle\Form\Type\ImportDefinitionType;
use Integrated\Bundle\StorageBundle\Storage\Cache\AppCache;
use Integrated\Common\Content\Form\ContentFormType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Config\FileLocator;

class ImportFile
{

    protected $storageCache;

    protected $documentManager;

    public function __construct(
        AppCache $storageCache,
        DocumentManager $documentManager
    )
    {
        $this->storageCache = $storageCache;
        $this->documentManager = $documentManager;
    }

    public function toArray(ImportDefinition $importDefinition) {

        $file = null;
        if ($importDefinition->getFileId()) {
            $file = $this->documentManager->find(
                File::class,
                $importDefinition->getFileId()
            );
        }

        if (!$file || !$file->getFile()) {
            throw new \Exception("File not available");
        }

        $mimeType = $file->getFile()->getMetadata()->getMimeType();
        $filePath = $this->storageCache->path($file->getFile())->getPathname();

        switch ($mimeType) {
            case "application/json":
                $data = json_decode($filePath, true);
                break;
            case "application/xml":
                $xmlNode = simplexml_load_file($filePath, 'SimpleXMLElement', LIBXML_NOCDATA);
                $alwaysArrayElements = [];
                $data = $this->xmlToArray($xmlNode, array(
                    'alwaysArray' => $alwaysArrayElements,
                    'autoText' => false,
                ));

                $data = json_encode($data);
                $data = json_decode($data,true);
                break;
            default:
                $spreadsheet = IOFactory::load($filePath);
                $worksheet = $spreadsheet->getActiveSheet();
                $data = $worksheet->toArray();
        }

        return $data;
    }


    /**
     * @param SimpleXMLElement $xml
     * @param array $options
     * @return array
     * @link http://outlandish.com/blog/xml-to-json/
     */
    public function xmlToArray(\SimpleXMLElement $xml, $options = array())
    {
        $defaults = array(
            'namespaceSeparator' => ':', //you may want this to be something other than a colon
            'attributePrefix'    => '@', //to distinguish between attributes and nodes with the same name
            'alwaysArray'        => array(), //array of xml tag names which should always become arrays
            'autoArray'          => true, //only create arrays for tags which appear more than once
            'textContent'        => '$', //key used for the text content of elements
            'autoText'           => true, //skip textContent key if node has no attributes or child nodes
            'keySearch'          => false, //optional search and replace on tag and attribute names
            'keyReplace'         => false //replace values for above search values (as passed to str_replace())
        );
        $options = array_merge($defaults, $options);
        $namespaces = $xml->getDocNamespaces();
        $namespaces[''] = null; //add base (empty) namespace

        //get attributes from all namespaces
        $attributesArray = array();
        foreach ($namespaces as $prefix => $namespace) {
            foreach ($xml->attributes($namespace) as $attributeName => $attribute) {
                //replace characters in attribute name
                if ($options['keySearch']) $attributeName =
                    str_replace($options['keySearch'], $options['keyReplace'], $attributeName);
                $attributeKey = $options['attributePrefix']
                    . ($prefix ? $prefix . $options['namespaceSeparator'] : '')
                    . $attributeName;
                $attributesArray[$attributeKey] = (string)$attribute;
            }
        }

        //get child nodes from all namespaces
        $tagsArray = array();
        foreach ($namespaces as $prefix => $namespace) {
            foreach ($xml->children($namespace) as $childXml) {
                //recurse into child nodes
                $childArray = self::xmlToArray($childXml, $options);
                list($childTagName, $childProperties) = each($childArray);

                //replace characters in tag name
                if ($options['keySearch']) $childTagName =
                    str_replace($options['keySearch'], $options['keyReplace'], $childTagName);
                //add namespace prefix, if any
                if ($prefix) $childTagName = $prefix . $options['namespaceSeparator'] . $childTagName;

                if (!isset($tagsArray[$childTagName])) {
                    //only entry with this key
                    //test if tags of this type should always be arrays, no matter the element count
                    $tagsArray[$childTagName] =
                        in_array($childTagName, $options['alwaysArray']) || !$options['autoArray']
                            ? array($childProperties) : $childProperties;
                }
                elseif (
                    is_array($tagsArray[$childTagName]) && array_keys($tagsArray[$childTagName])
                    === range(0, count($tagsArray[$childTagName]) - 1)
                ) {
                    //key already exists and is integer indexed array
                    $tagsArray[$childTagName][] = $childProperties;
                }
                else {
                    //key exists so convert to integer indexed array with previous value in position 0
                    $tagsArray[$childTagName] = array($tagsArray[$childTagName], $childProperties);
                }
            }
        }

        //get text content of node
        $textContentArray = array();
        $plainText = trim((string)$xml);
        if ($plainText !== '') $textContentArray[$options['textContent']] = $plainText;

        //stick it all together
        $propertiesArray = !$options['autoText'] || $attributesArray || $tagsArray || ($plainText === '')
            ? array_merge($attributesArray, $tagsArray, $textContentArray) : $plainText;

        //return node as array
        return array(
            $xml->getName() => $propertiesArray
        );
    }

}