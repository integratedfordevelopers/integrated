<?php

namespace Integrated\Bundle\ContentBundle\Twig\Extension;

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Common\Content\ContentInterface;
use Solarium\QueryType\Select\Result\Document;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DocumentExtension extends AbstractExtension
{
    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @param DocumentManager $documentManager
     */
    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('integrated_document', [$this, 'getDocument']),
        ];
    }

    /**
     * @param mixed $data
     *
     * @return ContentInterface|null
     *
     * @throws \Doctrine\ODM\MongoDB\LockException
     * @throws \Doctrine\ODM\MongoDB\Mapping\MappingException
     */
    public function getDocument($data)
    {
        if ($data instanceof ContentInterface) {
            return $data;
        }

        if ($data instanceof Document && isset($data['type_id'])) {
            return $this->documentManager->getRepository(Content::class)->find($data['type_id']);
        }

        if (\is_string($data) && $data != '') {
            return $this->documentManager->getRepository(Content::class)->find($data);
        }

        return null;
    }
}
