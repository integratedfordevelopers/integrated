<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Converter;

use Solarium\QueryType\Update\Query\Document\DocumentInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
interface ConverterInterface
{
    /**
     * Convert the object to a array of fields
     *
     * @param object $object
     * @return array|null
     */
    public function getFields($object);

    /**
     * Convert the object to a unique id
     *
     * @param object $object
     * @return string|null
     */
    public function getId($object);
}
