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
class ContentChoiceTransformer implements DataTransformerInterface
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
     * @throws \Exception
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        } elseif ($value instanceof ContentInterface) {
            return $value->getId();
        }

        throw new TransformationFailedException(sprintf('Expected integrated content, "%s" given', gettype($value)));
    }

    /**
     * @param mixed $value
     * @return null|object
     * @throws \Doctrine\ODM\MongoDB\LockException
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        } elseif (is_string($value)) {
            return $this->repo->find($value);
        }
        throw new TransformationFailedException(sprintf('Expected string, "%s" given', gettype($value)));
    }
}