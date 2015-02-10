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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Form\DataTransformerInterface;

class AuthorTransformer implements DataTransformerInterface
{
    public function __construct(ManagerRegistry $om)
    {
        $this->om = $om;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($array)
    {
        if($array == null) {
            return array('data', 'type_div',);
        }

        $collection = array(
            'authors' => array(),
            'types'   => array(),
        );

        foreach($array as $author) {
            if(!$author->getPerson()) {
                continue;
            }

            $collection['authors'][] = array(
                'id'   => $author->getPerson()->getId(),
                'text' => $author->getPerson()->getFirstName() . ' ' . $author->getPerson()->getLastName()
            );

            $collection['types'][] = '<div id="type_' . $author->getPerson()->getId() . '" style="margin-top: 10px;" class="input-group input-group-sm"><span class="input-group-addon">' . $author->getPerson()->getFirstName() . ' ' . $author->getPerson()->getLastName() . '</span><input type="text" class="form-control type-text change-name-author" name="' . $author->getPerson()->getId() . '" value="' . $author->getType() . '" placeholder="Type"></div>';
        }

        return array(
            'data'     => json_encode($collection['authors']),
            'type_div' => implode($collection['types'])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($array)
    {
        $dm         = $this->om->getManager();
        $collection = array();

        if(is_array($array) && isset($array['persons']) && isset($array['types']) && is_array($array['types'])) {
            foreach(explode(',', $array['persons']) as $person) {
                $result = $dm->getRepository('IntegratedContentBundle:Content\Relation\Person')->find($person);

                if($result && isset($array['types'][$person])) {
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