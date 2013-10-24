<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\MongoDB\Content\Document\Relation;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Integrated\MongoDB\Content\Document\File;
use Integrated\Common\ContentType\Mapping\Annotations as Content;

/**
 * Document type Relation\Company
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @ODM\Document(collection="content")
 * @Content\Document("Company")
 */
class Company extends AbstractRelation
{
    /**
     * @var File
     * @ODM\ReferenceOne(targetDocument="Integrated\MongoDB\Content\Document\File")
     */
    protected $logo;

    /**
     * Get the file of the document
     *
     * @return File
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set the logo of the document
     *
     * @param File $logo
     * @return $this
     */
    public function setLogo(File $logo)
    {
        $this->logo = $logo;
        return $this;

    }
}