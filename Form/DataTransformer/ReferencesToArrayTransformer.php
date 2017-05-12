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
     * ReferencesToArrayTransformer constructor.
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

        foreach ($value as $reference) {
            if ($reference instanceof Content) {
                $array[$reference->getId()] = $reference->getId();
            }
        }

        return $array;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        $references = $this->dm->getRepository(Content::class)
            ->createQueryBuilder()
            ->field('id')
            ->in($value)
            ->getQuery()
            ->getIterator();

        return new ArrayCollection($references->toArray());
    }
}