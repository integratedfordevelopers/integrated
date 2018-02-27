<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SlugBundle\Mapping\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * Slug annotation.
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 *
 * @Annotation
 * @Target({"PROPERTY"})
 */
final class Slug extends Annotation
{
    /**
     * @var array
     */
    public $fields = [];

    /**
     * @var string
     */
    public $separator = '-';

    /**
     * @var int
     */
    public $lengthLimit = 200;
}
