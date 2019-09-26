<?php

namespace Integrated\Bundle\ImportBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\UnitOfWork;
use Doctrine\ORM\EntityManager;
use Integrated\Bundle\ChannelBundle\Model\Config;
use Integrated\Bundle\ContentBundle\Doctrine\ContentTypeManager;
use Integrated\Bundle\ContentBundle\Document\Content\Article;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Author;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Connector;
use Integrated\Bundle\ContentBundle\Document\Content\File;
use Integrated\Bundle\ContentBundle\Document\Content\Image;
use Integrated\Bundle\ContentBundle\Document\Content\Relation\Person;
use Integrated\Bundle\ContentBundle\Document\Content\Taxonomy;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Bundle\ImportBundle\Document\Embedded\ImportField;
use Integrated\Bundle\ImportBundle\Document\ImportDefinition;
use Integrated\Bundle\ImportBundle\Form\Type\ImportDefinitionType;
use Integrated\Bundle\ImportBundle\Import\Provider\Doctrine;
use Integrated\Bundle\ImportBundle\Import\Provider\File as ImportFile;
use Integrated\Bundle\ImportBundle\Import\ImportProcessor;
use Integrated\Bundle\ImportBundle\Serializer\InitializedObjectConstructor;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Storage\Metadata as StorageMetadata;
use Integrated\Bundle\StorageBundle\Storage\Manager;
use Integrated\Bundle\StorageBundle\Storage\Reader\MemoryReader;
use Integrated\Common\Content\Form\ContentFormType;
use JMS\Serializer\Construction\UnserializeObjectConstructor;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sunra\PhpSimple\HtmlDomParser;
use Symfony\Component\HttpFoundation\Session\Session;

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
     * @var ImportProcessor
     */
    private $processor;

    /**
     * @var Doctrine
     */
    private $doctrine;

    /**
     * ImportController constructor.
     *
     * @param ContentTypeManager $contentTypeManager
     * @param DocumentManager    $documentManager
     * @param EntityManager      $entityManager
     * @param ImportFile         $importFile
     * @param Doctrine           $doctrine
     * @param Manager            $storageManager
     * @param ImportProcessor    $processor
     */
    public function __construct(
        ContentTypeManager $contentTypeManager,
        DocumentManager $documentManager,
        EntityManager $entityManager,
        ImportFile $importFile,
        Doctrine $doctrine,
        Manager $storageManager,
        ImportProcessor $processor
    ) {
        $this->contentTypeManager = $contentTypeManager;
        $this->documentManager = $documentManager;
        $this->entityManager = $entityManager;
        $this->importFile = $importFile;
        $this->doctrine = $doctrine;
        $this->storageManager = $storageManager;
        $this->processor = $processor;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $contentTypes = $this->contentTypeManager->getAll();

        $documents = $this->documentManager->getRepository(ImportDefinition::class)->findBy([], ['name' => 'asc']);

        return $this->render(
            'IntegratedImportBundle::index.html.twig',
            [
                'contentTypes' => $contentTypes,
                'documents' => $documents,
            ]
        );
    }

    /**
     * @param ImportDefinition $importDefinition
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cloneImport(ImportDefinition $importDefinition)
    {
        $copiedImportDefinition = clone $importDefinition;
        $copiedImportDefinition->setName('Copy of '.$copiedImportDefinition->getName());
        $copiedImportDefinition->setFileId(null);

        $this->documentManager->persist($copiedImportDefinition);
        $this->documentManager->flush();

        return $this->redirectToRoute('integrated_import_edit', ['importDefinition' => $copiedImportDefinition->getId()]);
    }

    /**
     * @param Request     $request
     * @param ContentType $type
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newImport(Request $request, ContentType $type)
    {
        $importDefinition = new ImportDefinition();
        $importDefinition->setContentType($type->getId());

        $form = $this->createCreateImportDefinitionForm($importDefinition);

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
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param Request          $request
     * @param ImportDefinition $importDefinition
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editImport(Request $request, ImportDefinition $importDefinition)
    {
        $form = $this->createCreateImportDefinitionForm($importDefinition);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->documentManager->flush();

            return $this->redirect(
                $this->generateUrl('integrated_import_index')
            );
        }

        return $this->render(
            'IntegratedImportBundle::edit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param Request          $request
     * @param ImportDefinition $importDefinition
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function chooseFile(Request $request, ImportDefinition $importDefinition)
    {
        $contentTypeFile = $this->importFile->getContentType();

        $file = false;
        $method = 'PUT';

        if ($importDefinition->getConnectionUrl() && $importDefinition->getConnectionQuery()) {
            return $this->redirect(
                $this->generateUrl('integrated_import_definition', ['importDefinition' => $importDefinition->getId()])
            );
        }

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
                'importDefinition' => $importDefinition,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param Request          $request
     * @param ImportDefinition $importDefinition
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function composeDefinition(Request $request, ImportDefinition $importDefinition)
    {
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '4G');

        try {
            if ($importDefinition->getConnectionUrl() && $importDefinition->getConnectionQuery()) {
                $data = $this->doctrine->toArray($importDefinition);
            } else {
                $data = $this->importFile->toArray($importDefinition);
            }

            $contentType = $this->documentManager->find(
                ContentType::class,
                $importDefinition->getContentType()
            );

            $context = new SerializationContext();
            $context->setSerializeNull(true);

            $serializer = SerializerBuilder::create()
            ->addMetadataDir(realpath(__DIR__.'/../../ContentBundle/Resources/serializer'))
            ->setObjectConstructor(new InitializedObjectConstructor(new UnserializeObjectConstructor()))
            ->build();
            $contentTypeFields = json_decode($serializer->serialize($contentType->create(), 'json', $context), true);

            $fields = $this->processor->getFields();

            foreach ($contentTypeFields as $contentTypeField => $contentTypeValue) {
                $contentTypeFields = [];
                if (is_array($contentTypeValue)) {
                    foreach ($contentTypeValue as $contentTypeField2 => $contentTypeValue2) {
                        $contentTypeFields[] = $contentTypeField.'.'.$contentTypeField2;
                    }
                } else {
                    $contentTypeFields[] = $contentTypeField;
                }
                foreach ($contentTypeFields as $contentTypeField) {
                    $matchCol = false;
                    if (!$importDefinition->getFields()) {
                        if (isset($data[0])) {
                            $col = 1;
                            foreach ($data[0] as $dataValue) {
                                $dataName = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $dataValue));
                                $contentTypeFieldName = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $contentTypeField));
                                if ($dataName == $contentTypeFieldName) {
                                    if (!$matchCol) {
                                        $matchCol = [$col];
                                    }
                                }
                                ++$col;
                            }
                        }
                    }
                    //check current field

                    $fields['field-'.$contentTypeField] = ['label' => $contentTypeField, 'matchCol' => $matchCol];
                }
            }

            $fields['author-author'] = ['label' => 'Author', 'matchCol' => false];
            $fields['meta-meta'] = ['label' => 'Metadata', 'matchCol' => false];

            $configs = $this->entityManager->getRepository(Config::class)->findAll();
            foreach ($configs as $config) {
                $fields['connector-'.$config->getId()] = ['label' => 'ID for '.$config->getName(), 'matchCol' => false];
            }

            $relations = $this->documentManager->getRepository(Relation::class)->findAll();
            foreach ($relations as $relation) {
                $fields['relation-'.$relation->getId()] = ['label' => 'Relation '.$relation->getName(), 'matchCol' => false];
            }

            if ($request->request->get('action') == 'go') {
                if (isset($data[0])) {
                    $cols = \count($data[0]);
                    $fields2 = [];
                    for ($col = 0; $col < $cols; ++$col) {
                        $mappedField = $request->request->get('col'.$col, null);
                        if ($mappedField) {
                            $field = new ImportField();
                            $field->setColumn($col);
                            $field->setSourceField($data[0][$col]);
                            $field->setMappedField($mappedField);
                            $fields2[] = $field;
                        }
                    }

                    $importDefinition->setFields($fields2);
                    $this->documentManager->flush();
                }

                return $this->redirectToRoute('integrated_import_summary', ['importDefinition' => $importDefinition->getId()]);
            }

            //do some checks to generate some warnings
            if ($importDefinition->getFields()) {
                foreach ($importDefinition->getFields() as $field) {
                    if (isset($fields[$field->getMappedField()])) {
                        if ($field->getSourceField()) {
                            $column = array_search($field->getSourceField(), $data[0]);
                            if ($column === false) {
                                $this->get('braincrafted_bootstrap.flash')->alert('Warning: field '.$field->getSourceField().' is not available in the import any more will be ignored');
                                continue;
                            }
                            if ($column != $field->getColumn()) {
                                $this->get('braincrafted_bootstrap.flash')->alert('Warning: column '.$field->getSourceField().' is on another position now');
                            }
                        } else {
                            $column = $field->getColumn();
                        }
                        if ($fields[$field->getMappedField()]['matchCol'] == false) {
                            $fields[$field->getMappedField()]['matchCol'] = [];
                        }
                        $fields[$field->getMappedField()]['matchCol'][] = $column;
                    } else {
                        $this->get('braincrafted_bootstrap.flash')->alert('Warning: mapped field is not available and will be ignored: '.$field->getMappedField());
                    }
                }
            }

            $columnItemCount = [];
            foreach ($data[0] as $columnName) {
                $columnItemCount[$columnName] = 0;
            }

            //display at least 2 sample rows for each column and minimum 20 rows, don't display the rest
            $rowNumber = 0;
            foreach ($data as $index => $row) {
                if ($rowNumber >= 1) {
                    $showThisRow = false;
                    if ($rowNumber <= 20) {
                        $showThisRow = true;
                    }
                    foreach ($row as $column => $value) {
                        if (!isset($columnItemCount[$column])) {
                            $columnItemCount[$column] = 0;
                        }
                        if ($columnItemCount[$column] >= 2) {
                            continue;
                        }
                        if (\is_array($value)) {
                            if (\count($value) > 0) {
                                ++$columnItemCount[$column];
                                $showThisRow = true;
                            }
                        } elseif ($value != '') {
                            ++$columnItemCount[$column];
                            $showThisRow = true;
                        }
                    }
                    if (!$showThisRow) {
                        unset($data[$index]);
                    }
                }
                ++$rowNumber;
            }

            //todo: id for connector
            //todo: relations match

            //prepare data for display
            foreach ($data as $index => $row) {
                foreach ($row as $index2 => $value2) {
                    if (\is_array($value2)) {
                        $data[$index][$index2] = implode(', ', $value2);
                    }
                }
            }
        } catch (\Exception $e) {
            $this->get('braincrafted_bootstrap.flash')->error('Unable to read import file: '.$e->getMessage().$e->getTraceAsString());
            $fields = [];
            $data = [];
        }

        return $this->render(
            'IntegratedImportBundle::composeDefinition.html.twig',
            [
                'importDefinition' => $importDefinition,
                'fields' => $fields,
                'data' => $data,
            ]
        );
    }

    public function summary(ImportDefinition $importDefinition)
    {
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '4G');

        if ($importDefinition->getConnectionUrl() && $importDefinition->getConnectionQuery()) {
            $data = $this->doctrine->toArray($importDefinition);
        } else {
            $data = $this->importFile->toArray($importDefinition);
        }

        //found out which fields are not in the definition
        $ignoredFields = $data[0];
        foreach ($importDefinition->getFields() as $field) {
            $index = array_search($field->getSourceField(), $ignoredFields);
            if ($index !== false) {
                unset($ignoredFields[$index]);
            }
        }

        return $this->render(
            'IntegratedImportBundle::summary.html.twig',
            [
                'records' => \count($data),
                'importDefinition' => $importDefinition,
                'ignoredFields' => $ignoredFields,
            ]
        );
    }

    public function run(ImportDefinition $importDefinition)
    {
        return $this->render(
            'IntegratedImportBundle::run.html.twig',
            [
                'importDefinition' => $importDefinition,
                'startTime' => time(),
            ]
        );
    }

    public function runExecute(Request $request, ImportDefinition $importDefinition)
    {
        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '4G');

        //close session to prevent session locking for other connections
        $session = new Session();
        $session->save();

        $start = $request->get('start', 1);

        $result = [];
        $result['done'] = true;
        $result['success'] = [];
        $result['warnings'] = [];
        $result['errors'] = [];

        if ($importDefinition->getConnectionUrl() && $importDefinition->getConnectionQuery()) {
            $data = $this->doctrine->toArray($importDefinition);
        } else {
            $data = $this->importFile->toArray($importDefinition);
        }

        $totalRowNumber = \count($data);
        $rowsPerRequest = max(20, min(200, (int) $totalRowNumber / 20));
        if ($start <= 1) {
            $start = 1;
            $rowsPerRequest = 3;
        }

        $rowNumber = -1;

        $contentType = $this->documentManager->find(
            ContentType::class,
            $importDefinition->getContentType()
        );

        $context = new SerializationContext();
        $context->setSerializeNull(true);

        $serializer = SerializerBuilder::create()
            ->addMetadataDir(realpath(__DIR__.'/../../ContentBundle/Resources/serializer'))
            ->setObjectConstructor(new InitializedObjectConstructor(new UnserializeObjectConstructor()))
            ->build();
        //$contentTypeFields = json_decode($serializer->serialize($contentType->create(), 'json', $context), true);

        $fieldMapping = [];
        foreach ($importDefinition->getFields() as $field) {
            $fieldMapping[$field->getSourceField()] = $field->getMappedField();
        }

        $newStart = $start;
        foreach ($data as $row) {
            ++$rowNumber;
            if ($rowNumber <= 0 || $rowNumber < $start) {
                //skip heading row and processed rows
                continue;
            }

            if (($newStart - $start) > $rowsPerRequest) {
                //max 20 items
                $result['start'] = $newStart;
                $result['done'] = false;
                continue;
            }

            $newStart = $newStart + 1;

            //create record
            $col = 0;
            $newData = [];
            foreach ($row as $value) {
                if (isset($fieldMapping[$data[0][$col]])) {
                    $mappedField = $fieldMapping[$data[0][$col]];

                    if ($mappedField) {
                        if (strpos($mappedField, 'field-') === 0) {
                            $mappedField = str_replace('field-', '', $mappedField);
                            $mappedFieldParts = explode(".", $mappedField);
                            if (count($mappedFieldParts) == 2) {
                                $newData[$mappedFieldParts[0]][$mappedFieldParts[1]] = $value;
                            } else {
                                $newData[$mappedField] = $value;
                            }
                        }
                    }
                }
                ++$col;
            }

            if (isset($newData['created_at'])) {
                //wordpress
                //todo: move to WP filter
                $newData['created_at'] = str_replace(' ', 'T', $newData['created_at']).'+2:00';
                //$newData['datecreated'] = date("Y-m-d\TH:i:s+1:00", $newData['datecreated']);
            }

            if (isset($newData['updated_at'])) {
                //wordpress
                //todo: move to WP filter
                $newData['updated_at'] = str_replace(' ', 'T', $newData['updated_at']).'+2:00';
            }

            if (isset($newData['start_date'])) {
                //wordpress
                //todo: move to WP filter
                if (strlen($newData['start_date']) == 10) {
                    $date =  new \DateTime($newData['start_date']);
                    $newData['start_date'] = $date->format(\DateTime::ISO8601);
//                    $newData['start_date'] = $newData['start_date'].'T00:00:00';
                }
                //$newData['datecreated'] = date("Y-m-d\TH:i:s+1:00", $newData['datecreated']);
            }

            if (isset($newData['end_date'])) {
                //wordpress
                //todo: move to WP filter
                if (strlen($newData['end_date']) == 10) {
                    $date =  new \DateTime($newData['end_date']);
                    $newData['end_date'] = $date->format(\DateTime::ISO8601);
//                        $newData['end_date'].'T00:00:00';
                }
                //$newData['datecreated'] = date("Y-m-d\TH:i:s+1:00", $newData['datecreated']);
            }

            if (\count($newData)) {
                $context = new DeserializationContext();
                $target = $contentType->create();

                if (isset($row['contentitem_id']) && $importDefinition->getImageBaseUrl()) {
                    $doubleArticle = $this->documentManager->getRepository(Content::class)->findOneBy([
                        'metadata.data.externalId' => $row['contentitem_id'],
                        'metadata.data.importImageBaseUrl' => $importDefinition->getImageBaseUrl(),
                    ]);
                    if ($doubleArticle) {
                        $result['warnings'][] = 'Item '.$row['contentitem_id'].' already imported - updating';
                        $target = $doubleArticle;
                    }
                }
                $context->setAttribute('target', $target);

                try {
                    $newObject = $serializer->deserialize(json_encode($newData), $contentType->getClass(), 'json', $context);
                } catch (RuntimeException $e) {
                    $result['errors'][] = 'Data error: '.$e->getMessage();
                    continue;
                }

                if ($importDefinition->getImageRelation()) {
                    if ($relation = $newObject->getRelation($importDefinition->getImageRelation()->getId())) {
                        $newObject->removeRelation($relation);
                    }
                    if ($relation = $newObject->getRelation('__editor_image')) {
                        $newObject->removeRelation($relation);
                    }
                }

                if ($newObject instanceof Person) {
                    if (strpos($newObject->getLastName(), ' ') !== false
                    &&
                        (empty($newObject->getFirstName()) ||  strpos($newObject->getLastName(), $newObject->getFirstName()) !== false)
                    ) {
                        list($firstName, $lastName) = explode(' ', $newObject->getLastName(), 2);
                        $newObject->setFirstName($firstName);
                        $newObject->setLastName($lastName);
                    }

                    if (!empty($row['picture_src'])) {
                        $path = false;
                        foreach ($importDefinition->getChannels() as $channel) {
                            $path = '/home/testpi-integrated/importfiles/'.$channel->getId().'/images/auteurfotos/'.$row['picture_src'];
                        }

                        if ($path !== false && file_exists($path)) {
                            $storage = $this->storageManager->write(
                                new MemoryReader(
                                    file_get_contents($path),
                                    new StorageMetadata(
                                        pathinfo($path, PATHINFO_EXTENSION),
                                        mime_content_type($path),
                                        new ArrayCollection(),
                                        new ArrayCollection()
                                    )
                                )
                            );
                            $newObject->setPicture($storage);
                        }
                    }
                }

                try {
                    //todo: move to Wordpress filter
                    if (isset($row['wp:post_id']) && $importDefinition->getImageBaseUrl()) {
                        //todo image base URL to general base URL
                        $doubleArticle = $this->documentManager->getRepository(Content::class)->findOneBy([
                            'metadata.data.wpPostId' => $row['wp:post_id'],
                            'metadata.data.importImageBaseUrl' => $importDefinition->getImageBaseUrl(),
                        ]);
                        if ($doubleArticle) {
                            $result['warnings'][] = 'Wordpress post '.$row['wp:post_id'].' already imported';
                            continue;
                        }
                    }



                    if (isset($row['wp:post_id']) && $importDefinition->getImageBaseUrl()) {
                        //todo image base URL to general base URL
                        $doubleArticle = $this->documentManager->getRepository(Content::class)->findOneBy([
                            'metadata.data.wpPostId' => $row['wp:post_id'],
                            'metadata.data.importImageBaseUrl' => $importDefinition->getImageBaseUrl(),
                        ]);
                        if ($doubleArticle) {
                            $result['warnings'][] = 'Wordpress post '.$row['wp:post_id'].' already imported';
                            continue;
                        }
                    }

                    //todo, make optional (or remove)
                    /*
                    $doubleArticle = $this->documentManager->getRepository(Article::class)->findOneBy(['title' => $newObject->getTitle()]);
                    if ($doubleArticle) {
                        //do not import duplicate articles, except for files
                        if (!$newObject instanceof File) {
                            //$result['warnings'][] = 'Item "'.$newObject->getTitle().'" already imported';
                            //continue;
                            $newObject->setContentType('food_artikel');
                            $result['warnings'][] = 'Item "'.$newObject->getTitle().'" already imported, place as food article';
                        }
                    }
                    */
                    if (isset($row['publiceren_van']) && $row['publiceren_van'] != '') {
                        $newObject->getPublishTime()->setStartDate(new \DateTime('@'.$row['publiceren_van']));
                    } else {
                        $newObject->getPublishTime()->setStartDate($newObject->getCreatedAt());
                    }

                    if (isset($row['publiceren_tot']) && $row['publiceren_tot'] != '') {
                        $newObject->getPublishTime()->setEndDate(new \DateTime('@'.$row['publiceren_tot']));
                    }

                    $imgIds = [];

                    foreach ($importDefinition->getChannels() as $channel) {
                        if ($newObject instanceof Person) {
                            if ($row['own_page'] != 1) {
                                continue;
                            }
                        }
                        $newObject->addChannel($channel);
                    }

                    $col = 0;
                    foreach ($row as $name => $value) {
                        if (!isset($fieldMapping[$data[0][$col]])) {
                            ++$col;
                            continue;
                        }
                        $mappedField = $fieldMapping[$data[0][$col]];

                        if (strpos($mappedField, 'author-') === 0) {
                            /** @var $newObject Article */
                            $newObject->getAuthors()->clear();
                            if (trim($value) != '') {
                                foreach (explode(',', $value) as $auteur) {
                                    if (trim($auteur) != '') {
                                        $author = new Author();
                                        $author->setPerson($this->addAuthor($auteur, $importDefinition->getImageBaseUrl(), $importDefinition->getAuthorContentType()));
                                        $newObject->addAuthor($author);
                                    }
                                }
                            }
                        }

                        if (strpos($mappedField, 'meta-') === 0) {
                            if (trim($value) != '') {
                                $newObject->getMetadata()->set($name, $value);
                            }
                        }

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
                            if ($relation = $newObject->getRelation($relationId)) {
                                $newObject->removeRelation($relation);
                            }

                            $relation = $this->documentManager->getRepository(Relation::class)->find($relationId);

                            $targets = $relation->getTargets();
                            $targetContentType = $targets[0];
                            //TODO: allow choose content type

                            /*$targetContentType = $this->documentManager->find(
                                ContentType::class,
                                $target
                            );*/

                            if ($value) {
                                $relation2 = new \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation();
                                $relation2->setRelationId($relation->getId());
                                $relation2->setRelationType($relation->getType());

                                if (!\is_array($value)) {
                                    $value = explode(',', $value);
                                }

                                foreach ($value as $valueName) {
                                    $link = false;
                                    $valueName = trim($valueName);
                                    if ($targetContentType->getClass() == Taxonomy::class || $targetContentType->getClass() == Article::class) {
                                        $link = $this->documentManager->getRepository(Content::class)->findOneBy(['title' => $valueName, 'contentType' => $targetContentType->getId()]);
                                    }

                                    if ($targetContentType->getClass() == Image::class) {
                                        $path = false;
                                        foreach ($importDefinition->getChannels() as $channel) {
                                            foreach (['header/original', 'header', 'editie/header'] as $folder) {
                                                if (!file_exists($path)) {
                                                    $path = '/home/testpi-integrated/importfiles/' . $channel->getId() . '/images/' . $folder . '/' . $valueName;
                                                }
                                            }
                                        }
                                        if ($path === false || !file_exists($path)) {
                                            $result['warnings'][] = 'File not found 1st: '.$path.' for '.$newObject->getTitle();
                                            continue;
                                        }
                                        $link = $this->documentManager->getRepository(Image::class)->findOneBy(['metadata.data.externalId' => 'header/'.$valueName, 'metadata.data.importImageBaseUrl' => $importDefinition->getImageBaseUrl()]);
                                    }

                                    if (!$link) {
                                        $link = $targetContentType->create();
                                        $link->setTitle($valueName);
                                        $link->getMetadata()->set('importDate', date('Ymd'));
                                        $link->getMetadata()->set('externalId', 'header/'.$valueName);
                                        $link->getMetadata()->set('importImageBaseUrl', $importDefinition->getImageBaseUrl());

                                        foreach ($importDefinition->getChannels() as $channel) {
                                            $link->addChannel($channel);
                                        }

                                        $this->documentManager->persist($link);
                                        $this->documentManager->flush();
                                    }

                                    if ($link instanceof Image) {
                                        $path = false;
                                        foreach ($importDefinition->getChannels() as $channel) {
                                            foreach (['header/original', 'header', 'editie/header'] as $folder) {
                                                if (!file_exists($path)) {
                                                    $path = '/home/testpi-integrated/importfiles/' . $channel->getId() . '/images/' . $folder . '/' . $valueName;
                                                }
                                            }
                                        }

                                        if ($path !== false && file_exists($path)) {
                                            $storage = $this->storageManager->write(
                                                new MemoryReader(
                                                    file_get_contents($path),
                                                    new StorageMetadata(
                                                        pathinfo($path, PATHINFO_EXTENSION),
                                                        mime_content_type($path),
                                                        new ArrayCollection(),
                                                        new ArrayCollection()
                                                    )
                                                )
                                            );
                                            $link->setFile($storage);

                                            if (!empty($row['credits'])) {
                                                $link->setCredits($row['credits']);
                                            }

                                            $imageAltName = str_replace('_src', '_alt', $name);
                                            if (!empty($row[$imageAltName])) {
                                                $link->setDescription($row[$imageAltName]);
                                            }
                                            if (!empty($row['image_footer'])) {
                                                $link->setDescription($row['image_footer']);
                                            }

                                            $this->documentManager->flush();
                                        } else {
                                            $result['warnings'][] = 'File not found: '.$path.' for '.$newObject->getTitle();
                                        }

                                    }

                                    $relation2->addReference($link);
                                }

                                $newObject->addRelation($relation2);
                            }
                        }

                        ++$col;
                    }

                    if ($newObject instanceof Article || $newObject instanceof Person) {
                        if ($newObject instanceof Article) {
                            $content = $newObject->getContent();
                        } else {
                            $content = $newObject->getDescription();
                        }

                        $content = preg_replace_callback(
                            '/\[gallery ids\="(.+?)".*?\]/',
                            function ($matches) use (&$imgIds) {
                                $imgIds = array_merge($imgIds, explode(',', $matches[1]));

                                return '';
                            },
                            $content
                        );

                        $youtubeRexEg = '/(?:https?:\/\/)?(?:www\.)?youtu\.?be(?:\.com)?\/?.*(?:watch|embed)?(?:.*v=|v\/|\/)([\w-_]+)/';
                        $content = preg_replace_callback($youtubeRexEg, function ($matches) {
                            if (\strlen(trim($matches[1])) == 11) {
                                return '[object type="youtube" id="'.trim($matches[1]).'"]';
                            }

                            return $matches[0];
                        }, $content);

                        $content = preg_replace('/\[caption.*?\]/', '', $content);
                        $content = str_ireplace('[/caption]', '', $content);

                        $content = str_ireplace('<div class="well">', '<div class="frame-general">', $content);

                        $newHtml = '';
                        $prevLine = '';
                        if (true) { //todo: more to wordpress filter, only for Wordpress
                            //todo: move to filter
                            $contentLines = [];
                            foreach (explode("\n", $content) as $contentLine) {
                                if (str_replace('-', '', $contentLine) == '') {
                                    $contentLine = '';
                                }
                                if (trim($contentLine) != '') {
                                    $contentLines[] = $contentLine;
                                }
                            }

                            foreach ($contentLines as $lineKey => $line) {
                                $line = trim($line);

                                $nextCouldBeLi = false;
                                if (isset($contentLines[$lineKey + 1])) {
                                    $nextLine = $contentLines[$lineKey + 1];
                                    $nextLine = trim($nextLine);
                                    if (substr($nextLine, -1, 1) != '.'
                                        && substr($nextLine, -1, 1) != '?'
                                        && trim(str_ireplace('&nbsp;', '', $nextLine)) != ''
                                        && (substr($nextLine, -1, 1) != '>' || substr($nextLine, -3, 3) == '/a>')) {
                                        $nextCouldBeLi = true;
                                    }
                                }

                                if (trim(strip_tags(str_replace('&nbsp;', '', $line))) != '' || $line == '<ul>' || $line == '</ul>') {
                                    if (substr($line, -3, 3) == 'h1>'
                                        || substr($line, -3, 3) == 'h2>'
                                        || substr($line, -3, 3) == 'h3>'
                                        || substr($line, -3, 3) == 'li>'
                                        || substr($line, -3, 3) == 'ul>'
                                        || substr($line, 0, 3) == '<li'
                                        || substr($line, 0, 3) == '<ul'
                                        || (substr($line, 0, 1) == '[' && substr($line, -1, 1) == ']')
                                    ) {
                                        //niks mee doen
                                        if ($prevLine == 'li') {
                                            $newHtml .= '</ul>';
                                        }
                                        $prevLine = '';
                                    } elseif ((\strlen(strip_tags($line)) < 90 || $prevLine == 'li')
                                        && substr($line, -1, 1) != '.'
                                        && substr($line, -1, 1) != '?'
                                        && (substr($line, -1, 1) != '>' || substr($line, -3, 3) == '/a>')
                                        && ($nextCouldBeLi || $prevLine == 'li')
                                    ) {
                                        if ($prevLine != 'li') {
                                            $newHtml .= '<ul>';
                                        }
                                        if (strpos($line, '- ') === 0) {
                                            $line = substr($line, 2);
                                        }
                                        $line = '<li>'.$line.'</li>';
                                        $prevLine = 'li';
                                    } else {
                                        if ($prevLine == 'li') {
                                            $newHtml .= '</ul>';
                                        }
                                        $line = '<p>'.$line.'</p>';
                                        $prevLine = 'p';
                                    }
                                } else {
                                    if ($prevLine == 'li') {
                                        $newHtml .= '</ul>';
                                    }
                                }
                                $newHtml .= $line."\n";
                            }

                            if ($prevLine == 'li') {
                                $newHtml .= '</ul>';
                            }
                        } else { //content as text
                            foreach (explode("\n", $content) as $line) {
                                $line = trim($line);
                                $line = '<p>'.$line.'</p>';
                                $newHtml .= $line."\n";
                            }
                        }

                        $html = HtmlDomParser::str_get_html($newHtml);
                        if ($html === false) {
                            if ($newHtml != '') {
                                $result['warnings'][] = 'No valid HTML for '.(string) $newObject.', content ignored'.$newHtml;
                            }
                            $html = HtmlDomParser::str_get_html('<p></p>');
                        }

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
                                $href = rtrim($importDefinition->getImageBaseUrl(), '/').$href;
                            }
                            $title = false;

                            if (strpos($href, '../../upload/') === 0) {
                                $href = str_replace('../../upload/', '', $href);
                                foreach ($importDefinition->getChannels() as $channel) {
                                    if (file_exists('/home/testpi-integrated/importfiles/'.$channel->getId().'/'.$href)) {
                                        $href = '/home/testpi-integrated/importfiles/'.$channel->getId().'/'.$href;
                                    }
                                }
                            }

                            if (stripos($href, '.png') === false
                                && stripos($href, '.jpg') === false
                                && stripos($href, '.jpeg') === false
                                && stripos($href, '.gif') === false
                                && stripos($href, '.pdf') === false
                            ) {
                                continue;
                            }

                            if (stripos($href, '.pdf') !== false
                                && (!$importDefinition->getFileContentType()
                                    || !$importDefinition->getFileRelation())) {
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

                            if (!$title) {
                                $title = basename($href);
                                $title = str_replace('.'.pathinfo($href, PATHINFO_EXTENSION), '', $title);
                            }

                            if ($title) {
                                $tmpfile = tempnam('/tmp/', 'img').'.'.pathinfo($href, PATHINFO_EXTENSION);
                                file_put_contents($tmpfile, @file_get_contents($href));
                                if (filesize($tmpfile) == 0) {
                                    //echo $file . "\n";
                                    //echo "FILE HAS 0 BYTES\n";
                                    unlink($tmpfile);
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

                                //if (stripos($href, '.pdf') !== false) {
                                    $contentTT = $importDefinition->getFileContentType();
                                    $file = $this->documentManager->getRepository(File::class)->findOneBy([
                                        'contentType' => $contentTT,
                                        'file.identifier' => $storage->getIdentifier(),
                                    ]);

                                    if (!$file) {
                                        $file = new File();
                                        $file->setContentType($contentTT);
                                        $file->setTitle($title);
                                        $file->setFile($storage);
                                        $file->getMetadata()->set('importDate', date('Ymd'));

                                        $this->documentManager->persist($file);
                                        $this->documentManager->flush($file);
                                    }

                                    $relation = new \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation();
                                    $relation->setRelationId($importDefinition->getFileRelation()->getId());
                                    $relation->setRelationType($importDefinition->getFileRelation()->getType());
                                    $relation->addReference($file);
                                    $newObject->addRelation($relation);

                                    $element->href = '/storage/'.$file->getId().'.'.pathinfo($href, PATHINFO_EXTENSION);

                                /*} else {
                                    $file = $this->documentManager->getRepository(Image::class)->findOneBy([
                                        'contentType' => $importDefinition->getImageContentType(),
                                        'file.identifier' => $storage->getIdentifier(),
                                    ]);
                                    if (!$file) {
                                        $file = new Image();
                                        $file->setContentType($importDefinition->getImageContentType());
                                        $file->setTitle($title);
                                        $file->setFile($storage);
                                        $file->getMetadata()->set('importDate', date('Ymd'));

                                        $this->documentManager->persist($file);
                                        $this->documentManager->flush($file);
                                    }

                                    $skipImage = false;
                                    if ($newObject->getReferencesByRelationType('embedded')) {
                                        foreach ($newObject->getReferencesByRelationType('embedded') as $reference) {
                                            if ($reference->getId() == $file->getId()) {
                                                $skipImage = true;
                                            }
                                        }
                                    }

                                    if (!$skipImage) {
                                        $relation = new \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation();
                                        $relation->setRelationId('__editor_image');
                                        $relation->setRelationType('embedded');
                                        $relation->addReference($file);
                                        $newObject->addRelation($relation);
                                    }

                                    $element->outertext = '<img src="/storage/'.$file->getId().'.pdf"'
                                        .'class="img-responsive" title="'.htmlspecialchars($title).'"'
                                        .'alt="'.htmlspecialchars($title).'" data-integrated-id="'.$file->getId().'" />';
                                }*/
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
                                $href = rtrim($importDefinition->getImageBaseUrl(), '/').$href;
                            }

                            if (strpos($href, '../../upload/') === 0) {
                                $href = str_replace('../../upload/', '', $href);
                                foreach ($importDefinition->getChannels() as $channel) {
                                    if (file_exists('/home/testpi-integrated/importfiles/'.$channel->getId().'/'.$href)) {
                                        $href = '/home/testpi-integrated/importfiles/'.$channel->getId().'/'.$href;
                                    }
                                }
                            }

                            /*
                             * Wordpress
                             */
                            $image = $this->documentManager->getRepository(Image::class)->findOneBy([
                                'metadata.data.wpUrl' => $href,
                                'metadata.data.importImageBaseUrl' => $importDefinition->getImageBaseUrl(),
                            ]);
                            if ($image) {
                                //attach existing images instead of duplication

                                $skipImage = false;
                                if ($newObject->getReferencesByRelationType('embedded')) {
                                    foreach ($newObject->getReferencesByRelationType('embedded') as $reference) {
                                        if ($reference->getId() == $image->getId()) {
                                            $skipImage = true;
                                        }
                                    }
                                }

                                if (!$skipImage) {
                                    $relation = new \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation();
                                    $relation->setRelationId('__editor_image');
                                    $relation->setRelationType('embedded');
                                    $relation->addReference($image);
                                    $newObject->addRelation($relation);
                                }

                                $img->outertext = '<img src="/storage/'.$image->getId().'.jpg" class="img-responsive"'
                                    .' title="'.htmlspecialchars($image->getTitle()).'" alt="'.htmlspecialchars($image->getTitle()).'"'
                                    .' data-integrated-id="'.$image->getId().'" />';
                                continue;
                            }

                            if (!$title) {
                                $title = basename($img->src);
                                $title = str_replace('.png', '', $title);
                                $title = str_replace('.jpg', '', $title);
                                $title = str_replace('.jpeg', '', $title);
                                $title = str_replace('.gif', '', $title);
                            }

                            $tmpfile = tempnam('/tmp/', 'img').'.'.pathinfo($href, PATHINFO_EXTENSION);
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

                            $file = $this->documentManager->getRepository(Image::class)->findOneBy([
                                'contentType' => $importDefinition->getImageContentType(),
                                'file.identifier' => $storage->getIdentifier(),
                            ]);
                            if (!$file) {
                                $file = new Image();
                                $file->setContentType($importDefinition->getImageContentType());
                                $file->setTitle($title);
                                $file->setFile($storage);
                                $file->getMetadata()->set('importDate', date('Ymd'));

                                $this->documentManager->persist($file);
                                $this->documentManager->flush($file);
                            }

                            $skipImage = false;
                            if ($newObject->getReferencesByRelationType('embedded')) {
                                foreach ($newObject->getReferencesByRelationType('embedded') as $reference) {
                                    if ($reference->getId() == $file->getId()) {
                                        $skipImage = true;
                                    }
                                }
                            }

                            if (!$skipImage) {
                                $relation = new \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation();
                                $relation->setRelationId('__editor_image');
                                $relation->setRelationType('embedded');
                                $relation->addReference($file);
                                $newObject->addRelation($relation);
                            }

                            $img->outertext = '<img src="/storage/'.$file->getId().'.jpg" class="img-responsive" title="'.htmlspecialchars($title).'" alt="'.htmlspecialchars($title).'" data-integrated-id="'.$file->getId().'" />';
                        }

                        $html = (string) $html;

                        if ($newObject instanceof Article) {
                            $newObject->setContent($html);
                        } else {
                            $newObject->setDescription($html);
                        }
                    }

                    if (isset($row['wp:attachment_url']) && $newObject instanceof File) {
                        $tmpBaseFile = tempnam('/tmp/', 'img');
                        $tmpfile = $tmpBaseFile.'.'.pathinfo($row['wp:attachment_url'], PATHINFO_EXTENSION);
                        rename($tmpBaseFile, $tmpfile);
                        file_put_contents($tmpfile, @file_get_contents($row['wp:attachment_url']));
                        if (filesize($tmpfile) == 0) {
                            //echo $file . "\n";
                            //echo "FILE HAS 0 BYTES\n";
                            $result['errors'][] = 'Attachment '.$row['wp:post_id'].' has 0 bytes';
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
                        $image = $this->documentManager->getRepository(Image::class)->findOneBy([
                            'metadata.data.wpPostId' => $imgId,
                            'metadata.data.importImageBaseUrl' => $importDefinition->getImageBaseUrl(),
                        ]);
                        if ($image) {
                            $skipImage = false;
                            if ($newObject->getReferencesByRelationType('embedded')) {
                                foreach ($newObject->getReferencesByRelationType('embedded') as $reference) {
                                    if ($reference->getId() == $image->getId()) {
                                        $skipImage = true;
                                    }
                                }
                            }

                            if (!$skipImage) {
                                $relation = new \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation();
                                $relation->setRelationId($importDefinition->getImageRelation()->getId());
                                $relation->setRelationType('embedded');
                                $relation->addReference($image);
                                $newObject->addRelation($relation);
                            }
                        } else {
                            $result['warnings'][] = 'Image with ID '.$imgId.' not found';
                        }
                    }

                    if (isset($row['wp:post_id'])) {
                        $newObject->getMetadata()->set('wpPostId', $row['wp:post_id']);
                        $newObject->getMetadata()->set('wpUrl', (isset($row['wp:attachment_url'])) ? $row['wp:attachment_url'] : $row['link']);
                        $newObject->getMetadata()->set('importDate', date('Ymd'));
                        $newObject->getMetadata()->set('importImageBaseUrl', $importDefinition->getImageBaseUrl());
                    }

                    if (isset($row['contentitem_id'])) {
                        $newObject->getMetadata()->set('externalId', $row['contentitem_id']);
                        $newObject->getMetadata()->set('importDate', date('Ymd'));
                        $newObject->getMetadata()->set('importImageBaseUrl', $importDefinition->getImageBaseUrl());
                    }

                    if (isset($row['meta_yoast_wpseo_canonical']) && $newObject instanceof Article) {
                        $newObject->setSourceUrl($row['meta_yoast_wpseo_canonical']);
                    }

                    //premium articles
                    if ($newObject->getMetadata()->get('premium') == 1) {
                        if ($relation = $newObject->getRelation('premium')) {
                            $newObject->removeRelation($relation);
                        }

                        $premiumItem = $this->documentManager->getRepository(Taxonomy::class)->findOneBy(['contentType' => 'premium', 'title' => 'Premium']);
                        $relation = new \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation();
                        $relation->addReference($premiumItem);
                        $relation->setRelationType('taxonomy');
                        $relation->setRelationId('premium');
                        $newObject->addRelation($relation);
                    }

                    if ($this->documentManager->getUnitOfWork()->getDocumentState($newObject) !== UnitOfWork::STATE_MANAGED) {
                        $this->documentManager->persist($newObject);
                    }
                    $this->documentManager->flush();

                    $import_id = $newObject->getId();
                    if (isset($row['wp:post_id'])) {
                        $import_id = $row['wp:post_id'];
                    }

                    $result['success'][] = 'Item '.$import_id.' ('.(string)$newObject.') imported';
                } catch (\Exception $e) {
                    $result['errors'][] = 'Item '.(string) $newObject.' failed: '.$e->getMessage().' '.nl2br($e->getTraceAsString()).' '.$e->getFile().' '.$e->getLine();
                } catch (\Throwable $e) {
                    $result['errors'][] = 'Item '.(string) $newObject.' fatal: '.$e->getMessage().' '.nl2br($e->getTraceAsString()).' '.$e->getFile().' '.$e->getLine();
                }
            }
        }

        $startTime = (int) $request->get('startTime', time());
        $duration = time() - $startTime;
        $remaining = ($duration / ($newStart - 1)) * ($totalRowNumber - ($newStart - 1));
        if ($remaining >= 120) {
            $remaining = round($remaining / 60).' minutes';
        } else {
            $remaining = round($remaining).' seconds';
        }

        if ($result['done']) {
            $result['percentage'] = 100;
            $result['remaining'] = '';
        } else {
            $result['startRow'] = $newStart;
            $result['percentage'] = round((($newStart - 1) / $totalRowNumber) * 100);
            $result['remaining'] = 'Estimated remaining time: '.$remaining;
        }

        return new JsonResponse($result);
    }

    /**
     * Creates a form to edit an ImportDefinition document.
     *
     * @param ImportDefinition $importDefinition
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createCreateImportDefinitionForm(ImportDefinition $importDefinition)
    {
        $form = $this->createForm(
            ImportDefinitionType::class,
            $importDefinition,
            [
                'method' => 'POST',
            ]
        );

        $form->add('submit', SubmitType::class, ['label' => 'Save']);

        return $form;
    }

    protected function addAuthor($name, $baseUrl, $type)
    {
        if ($type === null) {
            throw new \Exception('Please choose an author content type');
        }

        $dm = $this->documentManager;

        $name = trim($name);
        if (stripos($name, 'by ') === 0) {
            $name = substr($name, 3);
        }

        while (strpos($name, '  ') !== false) {
            $name = str_replace('  ', ' ', $name);
        }

        if (strpos($name, ' ') !== false) {
            list($firstname, $lastname) = explode(' ', $name, 2);
        } else {
            $firstname = '';
            $lastname = $name;
        }

        if (trim($lastname) == '') {
            $lastname = '(empty)';
            //throw new Exception('Empty lastname: ' . $name); //@todo: waarom?
        }

        $person = $dm
            ->createQueryBuilder(Person::class)
            ->field('contentType')->equals($type)
            ->field('firstName')->equals($firstname)
            ->field('lastName')->equals($lastname)
            ->field('metadata.data.importImageBaseUrl')->equals($baseUrl)
            ->getQuery()
            ->getSingleResult();
        if (!$person) {
            $person = $dm
                ->createQueryBuilder(Person::class)
                ->field('contentType')->equals($type)
                ->field('firstName')->equals($firstname)
                ->field('lastName')->equals($lastname)
                ->getQuery()
                ->getSingleResult();
        }

        if (!$person) {
            $person = new Person();
            $person
                ->setContentType($type)
                ->setFirstname($firstname)
                ->setLastname($lastname);

            $dm->persist($person);
            $dm->flush($person);
        }

        return $person;
    }
}
