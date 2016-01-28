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

use Symfony\Component\Form\DataTransformerInterface;

use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Bundle\ContentBundle\Document\Content\Content;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class ContentChoiceTransformer implements DataTransformerInterface
{
    /**
     * @var \Integrated\Bundle\ContentBundle\Document\Content\ContentRepository
     */
    protected $repo;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param DocumentManager $dm
     * @param array $options
     */
    public function __construct(DocumentManager $dm, array $options)
    {
        $this->repo = $dm->getRepository($options['repositoryClass']);
        $this->options = $options;
    }

    /**
     * @param mixed $value
     * @return array|null
     */
    public function transform($value)
    {
        if ($value instanceof Content) {
            return [[
                'id' => $value->getId(),
                //todo publishable title INTEGRATED-364
                'text' => $value->getTitle()
            ]];
        }
    }

    /**
     * @param mixed $value
     * @return array|null|object
     */
    public function reverseTransform($value)
    {

        if ($value) {
            return $this->repo->find($value);
        }
    }
}