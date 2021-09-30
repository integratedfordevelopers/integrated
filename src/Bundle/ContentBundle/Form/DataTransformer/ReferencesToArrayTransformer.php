<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class ReferencesToArrayTransformer implements DataTransformerInterface
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @param DocumentManager $dm
     */
    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        $array = [];

        if (null === $value) {
            return $array;
        }

        foreach ($value as $reference) {
            if ($reference instanceof Content) {
                $array[$reference->getId()] = (string) $reference;
            }
        }

        return $array;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (!$value) {
            return new ArrayCollection();
        }

        $references = $this->dm->createQueryBuilder(Content::class)
            ->field('id')->in($value)
            ->getQuery()
            ->getIterator()
            ->toArray();

        if (!$references) {
            throw new TransformationFailedException(sprintf(
                'A content with ID "%s" does not exist!',
                $value
            ));
        }

        if (\count($references) != \count($value)) {
            throw new TransformationFailedException('Not all Contents could be fetched.');
        }

        return new ArrayCollection($references);
    }
}
