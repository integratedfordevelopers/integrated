<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Doctrine\ODM\Migration;

use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Field;
use Symfony\Component\Validator\Exception\NoSuchMetadataException;

/**
 * @author Johan Liefers <johan@-eactive.nl>
 * @author Koen Prins <koen@-eactive.nl>
 */
trait ContentTypeHelper
{
    /**
     * @param string $id
     * @param string $name
     * @param string $class
     * @param array  $requiredFields
     * @param array  $optionalFields
     * @param array  $options
     *
     * @return ContentType
     */
    protected function addContentType(
        $id,
        $name,
        $class,
        array $requiredFields = [],
        array $optionalFields = [],
        array $options = []
    ) {
        $this->write(sprintf('Creating contentType with id: "%s"', $id));

        $dm = $this->getDocumentManager();

        if (!$contentType = $dm->getRepository(ContentType::class)->find($id)) {
            $contentType = new ContentType();
            $contentType->setId($id);
            $dm->persist($contentType);
        }

        $contentType->setName($name)
            ->setClass($class)
            ->setOptions($options);

        if ($requiredFields || $optionalFields) {
            $this->setContentTypeFields($contentType, $requiredFields, $optionalFields);
        }

        $dm->flush($contentType);

        return $contentType;
    }

    /**
     * @param ContentType $contentType
     * @param array       $requiredFields
     * @param array       $optionalFields
     *
     * @return ContentType
     */
    protected function setContentTypeFields(
        ContentType $contentType,
        array $requiredFields = [],
        array $optionalFields = []
    ) {
        $contentType->setFields([]);

        return $this->addContentTypeFields($contentType->getId(), $requiredFields, $optionalFields);
    }

    /**
     * @param string $contentTypeId
     * @param array  $requiredFields
     * @param array  $optionalFields
     *
     * @return ContentType
     */
    public function addContentTypeFields($contentTypeId, array $requiredFields = [], array $optionalFields = [])
    {
        $contentType = $this->getContentType($contentTypeId);

        $metadataFactory = $this->getContainer()->get('integrated_content.metadata.factory');

        if (!$metadata = $metadataFactory->getMetadata($contentType->getClass())) {
            throw new NoSuchMetadataException(
                sprintf('No class metadata defined for class "%s"', $contentType->getClass())
            );
        }
        $requiredFields = array_map('strtolower', $requiredFields);
        $optionalFields = array_map('strtolower', $optionalFields);

        $fields = $contentType->getFields();

        if ($fields instanceof Collection) {
            $fields = $fields->toArray();
        }

        foreach ($metadata->getFields() as $field) {
            if (!\in_array(strtolower($field->getName()), array_merge($optionalFields, $requiredFields))) {
                continue;
            }

            $fields[$field->getName()] = (new Field())
                    ->setName($field->getName())
                    ->setOptions(['required' => \in_array(strtolower($field->getName()), $requiredFields)]);
        }

        $contentType->setFields($fields);

        $this->getDocumentManager()->flush($contentType);

        return $contentType;
    }

    /**
     * @param string $contentTypeId
     *
     * @return ContentType
     *
     * @throws DocumentNotFoundException
     */
    public function updateContentTypeFields($contentTypeId)
    {
        $contentType = $this->getContentType($contentTypeId);

        $fields = $contentType->getFields();

        $requiredFields = [];
        $optionalFields = [];
        foreach ($fields as $field) {
            $options = $field->getOptions();
            if (isset($options['required']) && $options['required']) {
                $requiredFields[] = $field->getName();
            } else {
                $optionalFields[] = $field->getName();
            }
        }

        return $this->setContentTypeFields($contentType, $requiredFields, $optionalFields);
    }

    /**
     * @param string $contentTypeId
     * @param array  $removeFields
     *
     * @return ContentType
     */
    public function removeContentTypeFields($contentTypeId, array $removeFields)
    {
        $contentType = $this->getContentType($contentTypeId);

        $fields = $contentType->getFields();

        if ($fields instanceof Collection) {
            $fields = $fields->toArray();
        }

        foreach ($fields as $key => $field) {
            if (\in_array($field->getName(), $removeFields)) {
                unset($fields[$key]);
            }
        }

        $contentType->setFields($fields);

        $this->getDocumentManager()->flush($contentType);

        return $contentType;
    }

    /**
     * @param string $id
     * @param bool   $throwNotFoundException
     *
     * @return ContentType|null
     *
     * @throws DocumentNotFoundException
     */
    public function getContentType($id, $throwNotFoundException = true)
    {
        $contentType = $this->getDocumentManager()->getRepository(ContentType::class)->find($id);

        if (!$contentType && $throwNotFoundException) {
            throw new DocumentNotFoundException(sprintf('ContentType "%s" not found.', $id));
        }

        return $contentType;
    }

    /**
     * @return ContentType[]
     */
    public function getContentTypes()
    {
        return $this->getDocumentManager()->getRepository(ContentType::class)->findAll();
    }

    /**
     * @param string $id
     */
    protected function removeContentType($id)
    {
        $dm = $this->getDocumentManager();
        $contentType = $dm->getRepository(ContentType::class)->find($id);

        if ($contentType) {
            $dm->remove($contentType);
            $dm->flush($contentType);

            $this->write(sprintf('Removed contentType with id "%s".', $id));
        }
    }

    /**
     * @param string $message
     */
    abstract protected function write($message);

    /**
     * @return \Doctrine\ODM\MongoDB\DocumentManager
     */
    abstract protected function getDocumentManager();

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    abstract public function getContainer();
}
