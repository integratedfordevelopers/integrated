<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\MongoDB\Serializer\Normalizer;

use Doctrine\ODM\MongoDB\DocumentManager;
use Exception;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class DocumentNormalizer implements NormalizerInterface, DenormalizerInterface
{
    use SerializerAwareTrait;

    /**
     * @var DocumentManager
     */
    protected $dm = null;

    /**
     * @param DocumentManager $dm
     */
    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    /**
     * @return DocumentManager
     */
    protected function getDocumentManager()
    {
        return $this->dm;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        try {
            $document = $this->getDocumentManager()->getRepository($class)->find($data);
        } catch (Exception $e) {
            return null;
        }

        return $document;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $meta = $this->getDocumentManager()->getClassMetadata(\get_class($object));

        $keys = [];

        foreach ($meta->getIdentifierFieldNames() as $field) {
            $keys[$field] = $meta->getFieldValue($object, $field);
        }

        return $keys;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        if (!\is_array($data)) {
            return false;
        }

        return $this->supports($type);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $this->supports(\get_class($data));
    }

    /**
     * Check if the class is a mongodb document class registered by the
     * registered document manager.
     *
     * @param string $class
     *
     * @return bool
     */
    protected function supports($class)
    {
        $meta = $this->getDocumentManager()->getClassMetadata($class);

        if ($meta->isMappedSuperclass || $meta->isEmbeddedDocument) {
            return false;
        }

        $identifier = $meta->getIdentifierFieldNames();

        if (empty($identifier)) {
            return false;
        }

        return true;
    }
}
