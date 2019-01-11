<?php

namespace Integrated\Bundle\ImportBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManager;
use Integrated\Bundle\ChannelBundle\Model\Config;
use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Integrated\Bundle\ContentBundle\Doctrine\ContentTypeManager;
use Integrated\Bundle\ContentBundle\Document\Content\Article;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Connector;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\PublishTime;
use Integrated\Bundle\ContentBundle\Document\Content\File;
use Integrated\Bundle\ContentBundle\Document\Content\Image;
use Integrated\Bundle\ContentBundle\Document\Content\Taxonomy;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Field;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Bundle\ImportBundle\Document\Embedded\ImportField;
use Integrated\Bundle\ImportBundle\Document\ImportDefinition;
use Integrated\Bundle\ImportBundle\Form\Type\ImportDefinitionType;
use Integrated\Bundle\ImportBundle\Import\ImportFile;
use Integrated\Bundle\ImportBundle\Serializer\InitializedObjectConstructor;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Storage\Metadata as StorageMetadata;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Metadata;
use Integrated\Bundle\StorageBundle\Storage\Manager;
use Integrated\Bundle\StorageBundle\Storage\Reader\MemoryReader;
use Integrated\Common\Content\Form\ContentFormType;
use JMS\Serializer\Construction\UnserializeObjectConstructor;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Sunra\PhpSimple\HtmlDomParser;

class ImportController extends Controller
{
    /**
     * @var ContentTypeManager
     */
    protected $contentTypeManager;

    /**
     * @var DocumentManager
     */
    protected $documentManager;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var ImportFile
     */
    protected $importFile;

    /**
     * @var Manager
     */
    protected $storageManager;

    /**
     * ImportController constructor.
     * @param ContentTypeManager $contentTypeManager
     * @param DocumentManager $documentManager
     * @param EntityManager $entityManager
     * @param ImportFile $importFile
     * @param Manager $storageManager
     */
    public function __construct(
        ContentTypeManager $contentTypeManager,
        DocumentManager $documentManager,
        EntityManager $entityManager,
        ImportFile $importFile,
        Manager $storageManager
    ) {
        $this->contentTypeManager = $contentTypeManager;
        $this->documentManager = $documentManager;
        $this->entityManager = $entityManager;
        $this->importFile = $importFile;
        $this->storageManager = $storageManager;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $contentTypes = $this->contentTypeManager->getAll();

        $documents = $this->documentManager->getRepository(ImportDefinition::class)->findBy([], ['title' => 'asc']);

        return $this->render(
            'IntegratedImportBundle::index.html.twig',
            [
                'contentTypes' => $contentTypes,
                'documents' => $documents,
            ]
        );
    }

    /**
     * @param Request $request
     * @param ContentType $type
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newImport(Request $request, ContentType $type)
    {
        $importDefinition = new ImportDefinition();
        $importDefinition->setContentType($type->getId());

        $form = $this->createCreateImportDefinitionForm($importDefinition, $type->getId());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->documentManager->persist($importDefinition);
            $this->documentManager->flush();

            return $this->redirect(
                $this->generateUrl('integrated_import_file', ['importDefinition' => $importDefinition->getId()])
            );
        }

        return $this->render(
            'IntegratedImportBundle::new.html.twig',
            [
                'contentType' => $type,
                'form' => $form->createView()
            ]
        );

    }

    /**
     * @param Request $request
     * @param ImportDefinition $importDefinition
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function chooseFile(Request $request, ImportDefinition $importDefinition)
    {
        $contentType = $this->documentManager->find(
            ContentType::class,
            $importDefinition->getContentType()
        );

        $contentTypeFile = $this->importFile->getContentType();

        $file = false;
        $method = 'PUT';
        if ($importDefinition->getFileId()) {
            $file = $this->documentManager->find(File::class, $importDefinition->getFileId());
        }

        if (!$file) {
            //file not yet uploaded, create a new one
            $file = new File();
            $file->setContentType('import_file');
            $method = 'POST';
        }

        if ($method == 'POST') {
            $form = $this->createForm(ContentFormType::class, $file, [
                'action' => $this->generateUrl(
                    'integrated_import_file',
                    ['importDefinition' => $importDefinition->getId()]
                ),
                'method' => $method,
                'content_type' => $contentTypeFile->getId(),
            ]);
        } else {
            $form = $this->createForm(ContentFormType::class, $file, [
                'action' => $this->generateUrl(
                    'integrated_import_file',
                    ['importDefinition' => $importDefinition->getId()]
                ),
                'method' => $method,
                'content_type' => $contentTypeFile->getId(),
                'attr' => ['class' => 'content-form', 'data-content-id' => $file->getId()],
            ]);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($method == 'POST') {
                $this->documentManager->persist($file);
            }
            $this->documentManager->flush();

            $importDefinition->setFileId($file->getId());
            $this->documentManager->flush();

            return $this->redirect(
                $this->generateUrl('integrated_import_definition', ['importDefinition' => $importDefinition->getId()])
            );
        }

        return $this->render(
            'IntegratedImportBundle::chooseFile.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }


    /**
     * @param Request $request
     * @param ImportDefinition $importDefinition
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function composeDefinition(Request $request, ImportDefinition $importDefinition)
    {
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '4G');

        $data = $this->importFile->toArray($importDefinition);

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
                if (is_array($value2)) {
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
                if (!in_array($index2, $startRow)) {
                    //make sure all fields are in the startRow
                    $startRow[] = $index2;
                }

                //convert to flat values or array, eliminate attributes
                if (is_array($value2)) {
                    if (isset($value2['$'])) {
                        $data[$index][$index2] = $value2['$'];
                        continue;
                    }

                    if (count($value2) == 0) {
                        $data[$index][$index2] = '';
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

        $newData = array($startRow);
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

        $contentType = $this->documentManager->find(
            ContentType::class,
            $importDefinition->getContentType()
        );

        $context = new SerializationContext();
        $context->setSerializeNull(true);

        $serializer = SerializerBuilder::create()
            ->addMetadataDir(realpath(__DIR__ . '/../../ContentBundle/Resources/serializer'))
            ->setObjectConstructor(new InitializedObjectConstructor(new UnserializeObjectConstructor()))
            ->build();
        $contentTypeFields = json_decode($serializer->serialize($contentType->create(), 'json', $context), true);

        $fields = ['' => ['label' => '- ignore -', 'matchCol' => false]];

        foreach ($contentTypeFields as $contentTypeField => $contentTypeValue) {
            $matchCol = false;
            if (!$importDefinition->getFields()) {
                if (isset($data[0])) {
                    $col = 1;
                    foreach ($data[0] as $dataValue) {
                        $dataName = strtolower(preg_replace("/[^A-Za-z0-9]/", '', $dataValue));
                        $contentTypeFieldName = strtolower(preg_replace("/[^A-Za-z0-9]/", '', $contentTypeField));
                        if ($dataName == $contentTypeFieldName) {
                            if (!$matchCol) {
                                $matchCol = $col;
                            }
                        }
                        $col++;
                    }
                }
            } else {
                //check current field
            }
            $fields['field-' . $contentTypeField] = ['label' => $contentTypeField, 'matchCol' => $matchCol];
        }

        $configs = $this->entityManager->getRepository(Config::class)->findAll();
        foreach ($configs as $config) {
            $fields['connector-' . $config->getId()] = ['label' => 'ID for ' . $config->getName(), 'matchCol' => false];
        }

        $relations = $this->documentManager->getRepository(Relation::class)->findAll();
        foreach ($relations as $relation) {
            $fields['relation-' . $relation->getId()] = ['label' => 'Relation ' . $relation->getName(), 'matchCol' => false];
        }

        if ($importDefinition->getFields()) {
            foreach ($importDefinition->getFields() as $field) {
                if (isset($fields[$field->getMappedField()])) {
                    $fields[$field->getMappedField()]['matchCol'] = $field->getColumn();
                    //todo: check column heading
                } else {
                    //todo: ERROR
                }
            }
        }

        if ($request->request->get('action') == 'go') {
            if (isset($data[0])) {
                $cols = count($data[0]);
                $fields2 = [];
                for ($col = 1; $col <= $cols; $col++) {
                    $mappedField = $request->request->get('col' . $col, null);
                    if ($mappedField) {
                        $field = new ImportField();
                        $field->setColumn($col);
                        $field->setMappedField($mappedField);
                        $fields2[] = $field;
                    }
                }

                $importDefinition->setFields($fields2);
                $this->documentManager->flush();
            }

            $doneRows = 0;
            $rowNumber = -1;
            $doneTitles = [];

            foreach ($data as $row) {
                $rowNumber++;
                if ($rowNumber <= 0 || $rowNumber < $request->request->get('startRow', 0)) {
                    //skip heading row
                    //echo 'skip row ' . $rowNumber . ' (' . print_r($row) . ')<br />';
                    continue;
                }

                //create record
                $col = 1;
                $newData = [];
                foreach ($row as $value) {
                    $mappedField = $request->request->get('col' . $col, null);

                    if ($mappedField) {
                        if (strpos($mappedField, 'field-') === 0) {
                            $newData[str_replace('field-', '', $mappedField)] = $value;
                        }
                    }

                    $col++;
                }

                if (isset($newData['created_at'])) {
                    //wordpress
                    //todo: move to WP filter
                    $newData['created_at'] = str_replace(' ', 'T', $newData['created_at']) . '+2:00';
                    //$newData['datecreated'] = date("Y-m-d\TH:i:s+1:00", $newData['datecreated']);
                }

                if (count($newData)) {
                    $context = new DeserializationContext();
                    $target = $contentType->create();

                    $context->setAttribute('target', $target);

                    try {
                        $newObject = $serializer->deserialize(json_encode($newData), $contentType->getClass(), 'json', $context);
                    } catch (RuntimeException $e) {
                        $this->get('braincrafted_bootstrap.flash')->error($e->getMessage());
                        continue;
                    }

                    $doubleArticle = $this->documentManager->getRepository(Article::class)->findOneBy(['title' => $newObject->getTitle()]);
                    if ($doubleArticle) {
                        //do not import duplicate articles, except for files
                        if (!$newObject instanceof File) {
                            $this->get('braincrafted_bootstrap.flash')->alert('Post "'.$newObject->getTitle().'" already imported');
                            continue;
                        }
                    }

                    if (isset($row['wp:post_id']) && $importDefinition->getImageBaseUrl()) {
                        //todo image base URL to general base URL
                        $doubleArticle = $this->documentManager->getRepository(Content::class)->findOneBy(['metadata.data.wpPostId' => $row['wp:post_id'], 'metadata.data.importImageBaseUrl' => $importDefinition->getImageBaseUrl()]);
                        if ($doubleArticle) {
                            $this->get('braincrafted_bootstrap.flash')->alert('Post '.$row['wp:post_id'].' already imported');
                            continue;
                        }
                    }

                    $this->get('braincrafted_bootstrap.flash')->success('Importing '.$row['wp:post_id'].'');

                    if (isset($row['publiceren_van']) && $row['publiceren_van'] != '') {
                        $newObject->getPublishTime()->setStartDate(new \DateTime('@'.$row['publiceren_van']));
                    } else {
                        $newObject->getPublishTime()->setStartDate($newObject->getCreatedAt());
                    }

                    if (isset($row['publiceren_tot']) && $row['publiceren_tot'] != '') {
                        $newObject->getPublishTime()->setEndDate(new \DateTime('@'.$row['publiceren_tot']));
                    }

                    $imgIds = [];
                    if ($newObject instanceof Article) {
                        $content = $newObject->getContent();

                        $newHtml = '';
                        $prevLine = '';
                        if (true) { //todo: more to wordpress filter, only for Wordpress
                            //todo: move to filter
                            foreach (explode("\n", $content) as $line) {
                                $line = trim($line);
                                if (trim(strip_tags($line)) != "") {
                                    if (substr($line, -3, 3) == 'h1>'
                                        || substr($line, -3, 3) == 'h2>'
                                        || substr($line, -3, 3) == 'h3>'
                                        || substr($line, -3, 3) == 'li>'
                                    ) {
                                        //niks mee doen
                                        if ($prevLine == 'li') {
                                            $newHtml .= '</ul>';
                                        }
                                        $prevLine = '';
                                    } elseif (strlen(strip_tags($line)) < 90
                                        && substr($line, -1, 1) != '.'
                                        && substr($line, -1, 1) != '?'
                                        && (substr($line, -1, 1) != '>' || substr($line, -3, 3) == '/a>')
                                    ) {
                                        if ($prevLine != 'li') {
                                            $newHtml .= '<ul>';
                                        }
                                        if (strpos($line, '- ') === 0) {
                                            $line = substr($line, 2);
                                        }
                                        $line = '<li>' . $line . '</li>';
                                        $prevLine = 'li';
                                    } else {
                                        if ($prevLine == 'li') {
                                            $newHtml .= '</ul>';
                                        }
                                        $line = '<p>' . $line . '</p>';
                                        $prevLine = 'p';
                                    }
                                }
                                $newHtml .= $line . "\n";
                            }

                            if ($prevLine == 'li') {
                                $newHtml .= '</ul>';
                            }
                        } else { //content as text
                            foreach (explode("\n", $content) as $line) {
                                $line = trim($line);
                                $line = '<p>' . $line . '</p>';
                                $newHtml .= $line . "\n";
                            }
                        }

                        $html = HtmlDomParser::str_get_html($newHtml);

                        foreach ($html->find('a') as $element) {
                            if (!$importDefinition->getImageContentType() || !$importDefinition->getImageRelation()) {
                                continue;
                            }

                            $href = $element->href;
                            if (strpos($href, '/') === 0) {
                                //todo: move to filter
                                if (!$importDefinition->getImageBaseUrl()) {
                                    continue;
                                }
                                $href = rtrim($importDefinition->getImageBaseUrl(), '/') . $href;
                            }
                            $title = false;

                            if (stripos($href, '.png') === false
                                && stripos($href, '.jpg') === false
                                && stripos($href, '.jpeg') === false
                                && stripos($href, '.gif') === false) {
                                continue;
                            }

                            foreach ($element->find('img') as $img) {
                                $title = $img->title;
                                if (!$title) {
                                    $title = basename($img->src);
                                    $title = str_replace('.png', '', $title);
                                    $title = str_replace('.jpg', '', $title);
                                    $title = str_replace('.jpeg', '', $title);
                                    $title = str_replace('.gif', '', $title);
                                }
                            }
                            if ($title) {
                                $tmpfile = tempnam("/tmp/", "img") .  "." . pathinfo($href, PATHINFO_EXTENSION);
                                file_put_contents($tmpfile, @file_get_contents($href));
                                if (filesize($tmpfile) == 0) {
                                    //echo $file . "\n";
                                    //echo "FILE HAS 0 BYTES\n";
                                    continue;
                                }

                                $storage = $this->storageManager->write(
                                    new MemoryReader(
                                        file_get_contents($tmpfile),
                                        new StorageMetadata(
                                            pathinfo($href, PATHINFO_EXTENSION),
                                            mime_content_type($tmpfile),
                                            new ArrayCollection(),
                                            new ArrayCollection()
                                        )
                                    )
                                );

                                $file = $this->documentManager->getRepository(Image::class)->findOneBy(['contentType' => $importDefinition->getImageContentType(), 'file.identifier' => $storage->getIdentifier()]);
                                if (!$file) {
                                    $file = new Image();
                                    $file->setContentType($importDefinition->getImageContentType());
                                    $file->setTitle($title);
                                    $file->setFile($storage);
                                    $file->setMetaData(new Metadata(['importDate' => date('Ymd')]));

                                    $this->documentManager->persist($file);
                                    $this->documentManager->flush($file);
                                }

                                $relation = new \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation();
                                $relation->setRelationId($importDefinition->getImageRelation()->getId());
                                $relation->setRelationType('embedded');
                                $relation->addReference($file);
                                $newObject->addRelation($relation);

                                $element->outertext = '<img src="/storage/' . $file->getId() . '.jpg" class="img-responsive" title="' . htmlspecialchars($title) . '" alt="' . htmlspecialchars($title) . '" data-integrated-id="' . $file->getId() . '" />';
                            }
                        }

                        $html = HtmlDomParser::str_get_html((string) $html);

                        $title = false;
                        foreach ($html->find('img') as $img) {
                            if (!$importDefinition->getImageContentType() || !$importDefinition->getImageRelation()) {
                                continue;
                            }

                            $title = $img->title;
                            $href = $img->src;
                            if (strpos($href, '/') === 0) {
                                //todo: move to filter
                                if (!$importDefinition->getImageBaseUrl()) {
                                    continue;
                                }
                                $href = rtrim($importDefinition->getImageBaseUrl(), '/') . $href;
                            }

                            /*
                             * Wordpress
                             */
                            $image = $this->documentManager->getRepository(Image::class)->findOneBy(['metadata.data.wpUrl' => $href, 'metadata.data.importImageBaseUrl' => $importDefinition->getImageBaseUrl()]);
                            if ($image) {
                                //attach existing images instead of duplication
                                $relation = new \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation();
                                $relation->setRelationId($importDefinition->getImageRelation()->getId());
                                $relation->setRelationType('embedded');
                                $relation->addReference($image);
                                $newObject->addRelation($relation);
                                continue;
                            }

                            if (!$title) {
                                $title = basename($img->src);
                                $title = str_replace('.png', '', $title);
                                $title = str_replace('.jpg', '', $title);
                                $title = str_replace('.jpeg', '', $title);
                                $title = str_replace('.gif', '', $title);
                            }

                            $tmpfile = tempnam("/tmp/", "img") .  "." . pathinfo($href, PATHINFO_EXTENSION);
                            file_put_contents($tmpfile, @file_get_contents($href));
                            if (filesize($tmpfile) == 0) {
                                //echo $file . "\n";
                                //echo "FILE HAS 0 BYTES\n";
                                continue;
                            }

                            $storage = $this->storageManager->write(
                                new MemoryReader(
                                    file_get_contents($tmpfile),
                                    new StorageMetadata(
                                        pathinfo($href, PATHINFO_EXTENSION),
                                        mime_content_type($tmpfile),
                                        new ArrayCollection(),
                                        new ArrayCollection()
                                    )
                                )
                            );

                            $file = $this->documentManager->getRepository(Image::class)->findOneBy(['contentType' => $importDefinition->getImageContentType(), 'file.identifier' => $storage->getIdentifier()]);
                            if (!$file) {
                                $file = new Image();
                                $file->setContentType($importDefinition->getImageContentType());
                                $file->setTitle($title);
                                $file->setFile($storage);
                                $file->setMetaData(new Metadata(['importDate' => date('Ymd')]));

                                $this->documentManager->persist($file);
                                $this->documentManager->flush($file);
                            }

                            $relation = new \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation();
                            $relation->setRelationId($importDefinition->getImageRelation()->getId());
                            $relation->setRelationType('embedded');
                            $relation->addReference($file);
                            $newObject->addRelation($relation);

                            $img->outertext = '<img src="/storage/' . $file->getId() . '.jpg" class="img-responsive" title="' . htmlspecialchars($title) . '" alt="' . htmlspecialchars($title) . '" data-integrated-id="' . $file->getId() . '" />';
                        }

                        $html = (string) $html;

                        $html = preg_replace_callback(
                            '/\[gallery ids\="(.+?)".*?\]/',
                            function ($matches) use (&$imgIds) {
                                $imgIds = array_merge($imgIds, explode(",", $matches[1]));
                                return '';
                            },
                            $html
                        );

                        $html = preg_replace('/\[caption.*?\]/', '', $html);
                        $html = str_ireplace('[/caption]', '', $html);

                        $newObject->setContent($html);

                    }

                    //to implement
                    $channel = $this->documentManager->getRepository(Channel::class)->findOneBy(
                        ['name' => 'ijs']
                    );
                    $newObject->addChannel($channel);
                    if (!$channel) {
                        throw new \Exception('No channel');
                    }

                    $col = 1;
                    foreach ($row as $value) {
                        $mappedField = $request->request->get('col' . $col, null);

                        if (strpos($mappedField, 'connector-') === 0) {
                            $connectorId = str_replace('connector-', '', $mappedField);
                            $connectorConfig = $this->entityManager->getRepository(Config::class)->find($connectorId);

                            $connector = new Connector();
                            $connector->setConfigId($connectorId);
                            $connector->setConfigAdapter($connectorConfig->getAdapter());
                            $connector->setExternalId($value);

                            $newObject->addConnector($connector);
                        }

                        if (strpos($mappedField, 'relation-') === 0) {
                            $relationId = str_replace('relation-', '', $mappedField);
                            $relation = $this->documentManager->getRepository(Relation::class)->find($relationId);

                            $targets = $relation->getTargets();
                            $targetContentType = $targets[0];

                            /*$targetContentType = $this->documentManager->find(
                                ContentType::class,
                                $target
                            );*/

                            if ($value) {
                                $relation2 = new \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation();
                                $relation2->setRelationId($relation->getId());
                                $relation2->setRelationType($relation->getType());

                                if (!is_array($value)) {
                                    $value = array($value);
                                }

                                foreach ($value as $valueName) {
                                    //allow choose and find correct content type
                                    $link = $this->documentManager->getRepository(Taxonomy::class)->findOneBy(['title' => $valueName]);
                                    if (!$link) {
                                        $link = $targetContentType->create();
                                        $link->setTitle($valueName);
                                        $link->setMetaData(new Metadata(['importDate' => date('Ymd')]));

                                        $this->documentManager->persist($link);
                                        $this->documentManager->flush();
                                    }

                                    $relation2->addReference($link);
                                }

                                $newObject->addRelation($relation2);
                            }

                        }

                        $col++;
                    }

                    $doneTitles[] = $newObject->getTitle();

                    if (isset($row['wp:attachment_url']) && $newObject instanceof File) {
                        $tmpBaseFile = tempnam("/tmp/", "img");
                        $tmpfile = $tmpBaseFile.".".pathinfo($row['wp:attachment_url'], PATHINFO_EXTENSION);
                        rename($tmpBaseFile, $tmpfile);
                        file_put_contents($tmpfile, @file_get_contents($row['wp:attachment_url']));
                        if (filesize($tmpfile) == 0) {
                            //echo $file . "\n";
                            //echo "FILE HAS 0 BYTES\n";
                            $this->get('braincrafted_bootstrap.flash')->error('Attachment '.$row['wp:post_id'].' has 0 bytes');
                            unlink($tmpfile);
                            continue;
                        }

                        $storage = $this->storageManager->write(
                            new MemoryReader(
                                file_get_contents($tmpfile),
                                new StorageMetadata(
                                    pathinfo($row['wp:attachment_url'], PATHINFO_EXTENSION),
                                    mime_content_type($tmpfile),
                                    new ArrayCollection(),
                                    new ArrayCollection()
                                )
                            )
                        );

                        $newObject->setFile($storage);
                        unlink($tmpfile);
                    }

                    if (isset($row['meta_thumbnail_id'])) {
                        $imgIds[] = $row['meta_thumbnail_id'];
                    }

                    //wordpress
                    foreach ($imgIds as $imgId) {
                        if (!$imgId) {
                            continue;
                        }
                        $image = $this->documentManager->getRepository(Image::class)->findOneBy(['metadata.data.wpPostId' => $imgId, 'metadata.data.importImageBaseUrl' => $importDefinition->getImageBaseUrl()]);
                        if ($image) {
                            $relation = new \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation();
                            $relation->setRelationId($importDefinition->getImageRelation()->getId());
                            $relation->setRelationType('embedded');
                            $relation->addReference($image);
                            $newObject->addRelation($relation);
                        } else {
                            $this->get('braincrafted_bootstrap.flash')->error('Image with ID ' . $imgId . ' not found');
                        }
                    }

                    if (isset($row['wp:post_id'])) {
                        $newObject->setMetaData(
                            new Metadata(
                                [
                                    'wpPostId' => $row['wp:post_id'],
                                    'wpUrl' => (isset($row['wp:attachment_url'])) ? $row['wp:attachment_url'] : $row['link'],
                                    'importDate' => date('Ymd'),
                                    'importImageBaseUrl' => $importDefinition->getImageBaseUrl(),
                                ]
                            )
                        );
                    }

                    $this->documentManager->persist($newObject);
                    $this->documentManager->flush();

                    //echo 'added ' . $rowNumber . ' (' . print_r($row) . ')<br />';
                }

                $doneRows++;
                if ($doneRows >= 50) {
                    $data = array_slice($data, 0, 20);

                    //prepare data for display
                    foreach ($data as $index => $row) {
                        foreach ($row as $index2 => $value2) {
                            if (is_array($value2)) {
                                $data[$index][$index2] = implode(', ', $value2);
                            }
                        }
                    }

                    return $this->render(
                        'IntegratedImportBundle::composeDefinition.html.twig',
                        [
                            'fields' => $fields,
                            'data' => $data,
                            'startRow' => $rowNumber+1,
                        ]
                    );
                }
            }

            return $this->redirectToRoute('integrated_content_content_index');
        } else {
            $data = array_slice($data, 0, 20);
        }

        //todo: id for connector
        //todo: relations match

        //prepare data for display
        foreach ($data as $index => $row) {
            foreach ($row as $index2 => $value2) {
                if (is_array($value2)) {
                    $data[$index][$index2] = implode(', ', $value2);
                }
            }
        }

        return $this->render(
            'IntegratedImportBundle::composeDefinition.html.twig',
            [
                'fields' => $fields,
                'data' => $data,
                'startRow' => 0,
            ]
        );
    }


    /**
     * Creates a form to edit an ImportDefinition document.
     *
     * @param ImportDefinition $importDefinition
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createCreateImportDefinitionForm(ImportDefinition $importDefinition, $contentType)
    {
        $form = $this->createForm(
            ImportDefinitionType::class,
            $importDefinition,
            [
                'action' => $this->generateUrl('integrated_import_new', ['type' => $contentType]),
                'method' => 'POST',
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Save']);

        return $form;
    }

}
