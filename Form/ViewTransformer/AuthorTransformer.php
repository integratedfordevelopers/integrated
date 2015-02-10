<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\FormTypeBundle\Form\ViewTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Form\DataTransformerInterface;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Author;

class AuthorTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($array)
    {
        $collection = array();

        foreach ($array as $author) {
            $collection[] = '<div id="type_' . $author['id'] . '" style="margin-top: 10px;" class="input-group input-group-sm"><span class="input-group-addon">' . $author['text'] . '</span><input type="text" class="form-control type-text change-name-author" name="' . $author['id'] . '" value="' . $author['type'] . '" placeholder="Type"></div>';
        }

        return array('data' => json_encode($array), 'html' => implode($collection));
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($array)
    {
        return $array;
    }
}