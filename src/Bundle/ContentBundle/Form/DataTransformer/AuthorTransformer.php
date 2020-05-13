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
use Doctrine\Common\Persistence\ManagerRegistry;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Author;
use Integrated\Bundle\ContentBundle\Document\Content\Relation\Person;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Jurre de Jongh <jurre@e-active.nl>
 */
class AuthorTransformer implements DataTransformerInterface
{
    /**
     * @var ManagerRegistry
     */
    private $mr;

    /**
     * AuthorTransformer constructor.
     *
     * @param ManagerRegistry $mr
     */
    public function __construct(ManagerRegistry $mr)
    {
        $this->mr = $mr;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($arrayCollection)
    {
        if ($arrayCollection == null) {
            return [];
        }

        $collection = [];

        $authorNameCount = [];
        foreach ($arrayCollection as $author) {
            if (!($author instanceof Author) || !$author->getPerson()) {
                continue;
            }
            $authorNameCount[] = (string) $author->getPerson();
        }
        $authorNameCount = array_count_values($authorNameCount);

        foreach ($arrayCollection as $author) {
            if (!($author instanceof Author) || !$author->getPerson()) {
                continue;
            }

            $name = (string) $author->getPerson();
            if ($authorNameCount[$name] > 1) {
                $name .= ' ('.$author->getPerson()->getContentType().')';
            }

            $collection[] = [
                'id' => $author->getPerson()->getId(),
                'text' => $name,
                'type' => $author->getType(),
            ];
        }

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($array)
    {
        $mr = $this->mr->getManager();
        $collection = [];

        if (\is_array($array) && isset($array['persons'], $array['types']) && \is_array($array['types'])) {
            foreach ($array['persons'] as $person) {
                $result = $mr->getRepository(Person::class)->find($person);

                if ($result && isset($array['types'][$person])) {
                    $author = new Author();
                    $author->setType($array['types'][$person]);
                    $author->setPerson($result);

                    $collection[] = $author;
                }
            }
        }

        return new ArrayCollection($collection);
    }
}
