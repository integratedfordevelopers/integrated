<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\ContentBundle\Document\Content\Relation;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Integrated\Common\ContentType\Mapping\Annotations as Content;
use Integrated\Bundle\ContentBundle\Document\Content\File;

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
     * @var string
     * @ODM\String
     * @Content\Field
     */
    protected $name;

    /**
     * @var File
     * @ODM\ReferenceOne(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\File")
     */
    protected $logo;

    /**
     * Get the name of the document
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name of the document
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

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