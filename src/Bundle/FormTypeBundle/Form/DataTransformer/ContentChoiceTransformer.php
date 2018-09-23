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

use Doctrine\ODM\MongoDB\DocumentRepository;
use Integrated\Common\Content\ContentInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

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
     * @param DocumentRepository $repo
     */
    public function __construct(DocumentRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param ContentInterface|null $value
     *
     * @return string|null
     *
     * @throws TransformationFailedException
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        } elseif ($value instanceof ContentInterface) {
            return $value->getId();
        }

        throw new TransformationFailedException(sprintf('Expected integrated content, "%s" given', \gettype($value)));
    }

    /**
     * @param string|null $value
     *
     * @return null|ContentInterface
     *
     * @throws TransformationFailedException
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        } elseif (\is_string($value)) {
            $result = $this->repo->find($value);

            if ($result instanceof ContentInterface) {
                return $result;
            }

            throw new TransformationFailedException(sprintf('Document with id "%s" not found', $value));
        }

        throw new TransformationFailedException(sprintf('Expected string, "%s" given', \gettype($value)));
    }
}
