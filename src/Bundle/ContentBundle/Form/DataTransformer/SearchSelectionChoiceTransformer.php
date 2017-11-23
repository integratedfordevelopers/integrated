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

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Integrated\Bundle\ContentBundle\Document\SearchSelection\SearchSelection;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class SearchSelectionChoiceTransformer implements DataTransformerInterface
{
    /**
     * @var DocumentRepository
     */
    private $repository;

    /**
     * @param DocumentRepository $repository
     */
    public function __construct(DocumentRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($searchSelection)
    {
        if ($searchSelection instanceof SearchSelection) {
            return $searchSelection->getId();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($id)
    {
        return $this->repository->find($id);
    }
}
