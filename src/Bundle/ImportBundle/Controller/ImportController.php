<?php

namespace Integrated\Bundle\ImportBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManager;
use Integrated\Bundle\ChannelBundle\Model\Config;
use Integrated\Bundle\ContentBundle\Doctrine\ContentTypeManager;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Connector;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\PublishTime;
use Integrated\Bundle\ContentBundle\Document\Content\File;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Field;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Bundle\ImportBundle\Document\Embedded\ImportField;
use Integrated\Bundle\ImportBundle\Document\ImportDefinition;
use Integrated\Bundle\ImportBundle\Form\Type\ImportDefinitionType;
use Integrated\Bundle\ImportBundle\Import\ImportFile;
use Integrated\Bundle\ImportBundle\Serializer\InitializedObjectConstructor;
use Integrated\Common\Content\Form\ContentFormType;
use JMS\Serializer\Construction\UnserializeObjectConstructor;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Config\FileLocator;

class ImportController extends Controller
{
    /**
     * @var ContentTypeManager
     */
    protected $contentTypeManager;

    protected $documentManager;

    protected $entityManager;

    protected $formFactory;

    protected $importFile;

    public function __construct(
        ContentTypeManager $contentTypeManager,
        DocumentManager $documentManager,
        EntityManager $entityManager,
        ImportFile $importFile
    )
    {
        $this->contentTypeManager = $contentTypeManager;
        $this->documentManager = $documentManager;
        $this->entityManager = $entityManager;
        $this->importFile = $importFile;
    }

    public function index()
    {
        $contentTypes = $this->contentTypeManager->getAll();

        //todo: load import definitions

        return $this->render(
            'IntegratedImportBundle::index.html.twig',
            [
                'contentTypes' => $contentTypes,
                'documents' => []
            ]
        );
    }

    public function new(Request $request, ContentType $type)
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

    public function chooseFile(Request $request, ImportDefinition $importDefinition)
    {
        dump($importDefinition);
        $contentType = $this->documentManager->find(
            ContentType::class,
            $importDefinition->getContentType()
        );
//        dump($contentType->getClass());

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

        $file = false;
        $method = 'PUT';
        if ($importDefinition->getFileId()) {
            $file = $this->documentManager->find(
                File::class,
                $importDefinition->getFileId()
            );
        }

        if (!$file) {
            $file = new File();
            $file->setContentType('import_file');
            $method = 'POST';
            $attr = [];
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

            dump($importDefinition);

            return $this->redirect(
                $this->generateUrl('integrated_import_definition', ['importDefinition' => $importDefinition->getId()])
            );
        }

        return $this->render(
            'IntegratedImportBundle::choosefile.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }


    public function composeDefinition(Request $request, ImportDefinition $importDefinition) {

        $data = $this->importFile->toArray($importDefinition);
        $data = $data['rss']['channel']['item'];
        $data = array_slice($data, 0, 20);
        foreach ($data as $index => $row) {
            foreach ($row as $index2 => $value) {
                if (is_array($value)) {
                    $valuet = '';
                    foreach ($value as $value2) {
                        if (!is_array($value2)) {
                            $valuet .=  $value2;
                        }
                    }
                    dump($index);
                    $data[$index][$index2] = $valuet;
                    dump($data);
                }
            }
        }
        dump($data);

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

        $relations = $this->entityManager->getRepository(Relation::class)->findAll();
        foreach ($relations as $relation) {
            $fields['relation-' . $relation->getId()] = ['label' => 'ID for ' . $relation->getName(), 'matchCol' => false];
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
                $fields = [];
                for ($col = 1; $col <= $cols; $col++) {
                    $mappedField = $request->request->get('col' . $col, null);
                    if ($mappedField) {
                        $field = new ImportField();
                        $field->setColumn($col);
                        $field->setMappedField($mappedField);
                        $fields[] = $field;
                    }
                }

                $importDefinition->setFields($fields);
                $this->documentManager->flush();
            }

            $rowNumber = -1;
            foreach ($data as $row) {
                $rowNumber++;
                if ($rowNumber <= 0) {
                    //skip heading row
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

                if (count($newData)) {
                    $context = new DeserializationContext();
                    $target = $contentType->create();

                    $context->setAttribute('target', $target);

                    $newObject = $serializer->deserialize(json_encode($newData), $contentType->getClass(), 'json', $context);

                    $content = $newObject->getContent();
                    preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $content, $matches);
                    dump($matches);

                    

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
                            $relation = $this->entityManager->getRepository(Relation::class)->find($relationId);

                            $targets = $relation->getTargets();
                            $target = $targets[0];

                            $targetContentType = $this->documentManager->find(
                                ContentType::class,
                                $target
                            );

                            $link = $this->entityManager->getRepository(Taxonomy::class)->findBy(['name' => $value]);
                            if (!$link) {
                                $link = $targetContentType->create();
                                $link->setName($value);

                                $this->documentManager->persist($link);
                                $this->documentManager->flush();
                            }

                            $relation = new \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation();
                            $relation->setRelationId($relation->getRelationId());
                            $relation->setRelationType($relation->getRelationType());
                            $relation->addReference($link);
                            $newObject->addRelation($relation);
                        }
                    }

                    $this->documentManager->persist($newObject);
                    $this->documentManager->flush();
                }
            }

            return $this->redirectToRoute('integrated_content_content_index');
        } else {
            $data = array_slice($data, 0, 20);
        }

        //todo: id for connector
        //todo: relations match


        return $this->render(
            'IntegratedImportBundle::composeDefinition.html.twig',
            [
                'fields' => $fields,
                'data' => $data,
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
