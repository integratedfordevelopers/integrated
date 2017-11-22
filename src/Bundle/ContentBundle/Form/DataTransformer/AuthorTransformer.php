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
use Symfony\Component\Form\DataTransformerInterface;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Author;

/**
 * @author Jurre de Jongh <jurre@e-active.nl>
 */
class AuthorTransformer implements DataTransformerInterface
{
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
            return array();
        }

        $collection = array();

        foreach ($arrayCollection as $author) {
            if (!($author instanceof Author) || !$author->getPerson()) {
                continue;
            }

            $collection[] = array(
                'id'   => $author->getPerson()->getId(),
                'text' => (string) $author->getPerson(),
                'type' => $author->getType()
            );
        }

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($array)
    {
        $mr         = $this->mr->getManager();
        $collection = array();

        if (is_array($array) && isset($array['persons'], $array['types']) && is_array($array['types'])) {
            foreach ($array['persons'] as $person) {
                $result = $mr->getRepository('IntegratedContentBundle:Content\Relation\Person')->find($person);

                if ($result && isset($array['types'][$person])) {
                    $author = new \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Author();
                    $author->setType($array['types'][$person]);
                    $author->setPerson($result);

                    $collection[] = $author;
                }
            }
        }

        return new ArrayCollection($collection);
    }
}