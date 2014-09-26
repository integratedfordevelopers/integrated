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
use Symfony\Component\Form\DataTransformerInterface;

class Author implements DataTransformerInterface
{
    public function __construct($om, $request)
    {
        $this->om      = $om;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($authors)
    {
        if ($authors === NULL) {
            return array('data', 'type_div');
        }

        $array = array();
        $div   = false;

        foreach($authors as $author) {
            if(!$author->getPerson()->getId()) {
                continue;
            }

            $array[] = array(
                'id'   => $author->getPerson()->getId(),
                'text' => $author->getPerson()->getNickName()
            );

            $div .= '<div id="type_' . $author->getPerson()->getId() . '" style="margin-top: 10px;" class="input-group input-group-sm"><span class="input-group-addon">' . $author->getPerson()->getNickName() . '</span><input type="text" class="form-control type-text" name="' . $author->getPerson()->getId() . '_type" value="' . $author->getType() . '" placeholder="Type"></div>';
        }

        return array(
            'data'     => json_encode($array),
            'type_div' => $div
        );
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($string)
    {
        $dm      = $this->om->getManager();
        $persons = explode(',', $string);
        $array   = array();

        foreach($persons as $person) {
            $result     = $dm->getRepository('IntegratedContentBundle:Content\Relation\Person')->find($person);
            $type       = $this->request->get($person . '_type');

            if($result) {
                $author = new \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Author();
                $author->setType($type);
                $author->setPerson($result);

                $array[] = $author;
            }
        }

        return new ArrayCollection($array);
    }
}