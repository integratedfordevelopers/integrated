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
use Integrated\Common\Content\RankableInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ContentRankTransformer implements DataTransformerInterface
{
    /**
     * @var DocumentRepository
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
        } elseif ($value instanceof RankableInterface) {
            return $value->getRank();
        }

        throw new TransformationFailedException(sprintf('Expected integrated rankable content, "%s" given', \gettype($value)));
    }

    /**
     * @param string|null $value
     *
     * @return ContentInterface|null
     *
     * @throws TransformationFailedException
     */
    public function reverseTransform($value)
    {
        if (null === $value || \is_string($value)) {
            return $value;
        }

        throw new TransformationFailedException(sprintf('Expected string, "%s" given', \gettype($value)));
    }
}
