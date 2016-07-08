<?php

namespace Integrated\Bundle\ContentBundle\DataFixtures\MongoDB\Extension;

use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
trait ContentTypeExtension
{
    /**
     * @return ContainerInterface
     */
    abstract public function getContainer();

    /**
     * @param $id
     * @return null|ContentType
     */
    public function contentType($id)
    {
        return $this->getContainer()
            ->get('doctrine.odm.mongodb.document_manager')
            ->getRepository(ContentType::class)->find($id)
        ;
    }
}
