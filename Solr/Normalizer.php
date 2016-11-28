<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Solr;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class Normalizer
{
    /**
     * Convert to lower case and remove all the diacritics from the string.
     */
    public static function normalize($query)
    {
        $query = preg_replace('/\p{Mn}/u', '', \Normalizer::normalize($query, \Normalizer::FORM_KD));
        $query = strtolower(trim($query));

        return preg_replace('/\s+/u', ' ', $query);
    }
}
