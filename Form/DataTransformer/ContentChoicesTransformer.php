<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\FormTypeBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Common\Content\ContentInterface;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class ContentChoicesTransformer implements DataTransformerInterface
{
    /**
     * @var \Doctrine\ODM\MongoDB\DocumentRepository
     */
    protected $repo;

    /**
     * @param DocumentManager $dm
     * @param string $repositoryClass
     */
    public function __construct(DocumentManager $dm, $repositoryClass)
    {
        $this->repo = $dm->getRepository($repositoryClass);
    }

    /**
     * @param mixed $value
     * @return array|null
     * @throws TransformationFailedException
     */
    public function transform($value)
    {
        if (is_array($value) || $value instanceof \Traversable) {
            $values = [];
            foreach ($value as $content) {
                if (!$content instanceof ContentInterface) {
                    throw new TransformationFailedException(sprintf('Expected integrated content, "%s" given', gettype($content)));
                }
                $values[] = $content->getId();
            }
            return $values;
        }
        return null;
    }

    /**
     * @param mixed $value
     * @return array
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return [];
        } elseif (is_array($value)) {
            $documents = [];
            foreach ($value as $id) {
                $documents[] = $this->repo->find($id);
            }
            return $documents;
        }

        throw new TransformationFailedException(sprintf('Expected array, "%s" given', gettype($value)));
    }
}