<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\JsonLD\Serializer;

use Integrated\Bundle\ContentBundle\Document\Content\Article;
use Integrated\Bundle\ContentBundle\Document\Content\Event;
use Integrated\Bundle\ContentBundle\Document\Content\JobPosting;
use Integrated\Bundle\ContentBundle\Document\Content\Relation\Company;
use Integrated\Bundle\ContentBundle\Document\Content\Relation\Person;
use Integrated\Bundle\ContentBundle\Document\Content\Taxonomy;

use Integrated\Common\Content\Serializer\JsonLDNormalizer;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Normalizer extends JsonLDNormalizer
{
    /**
     * {@inheritDoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return parent::supportsNormalization($data, $format) && (
            $data instanceof Article ||
            $data instanceof Event || // also instance of Article
            $data instanceof Company ||
            $data instanceof Person ||
            $data instanceof Taxonomy
        );
    }
}
