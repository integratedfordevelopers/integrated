<?php
/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
namespace Integrated\Bundle\ContentBundle\Document\ContentType\Embedded;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Embedded document Reference
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @ODM\EmbeddedDocument
 */
class Reference
{
    /**
     * @var \Integrated\Bundle\ContentBundle\Document\ContentType\ContentType
     * @ODM\ReferenceOne(targetDocument="Integrated\Bundle\ContentBundle\Document\ContentType\ContentType")
     */
    protected $contentType;

    /**
     * @var bool One or more references possible
     * @ODM\Boolean
     */
    protected $multiple;

    /**
     * @var bool Is the reference required
     * @ODM\Boolean
     */
    protected $required;


}