<?php
namespace Integrated\Bundle\ContentBundle\Document\Relation;

use Integrated\Bundle\ContentBundle\Document\File;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Document type Relation\Company
 *
 * @package Integrated\Bundle\ContentBundle\Document\Relation
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @ODM\Document(collection="content")
 */
class Company extends AbstractRelation
{
    /**
     * @var File
     * @ODM\ReferenceOne(targetDocument="Integrated\Bundle\ContentBundle\Document\File")
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