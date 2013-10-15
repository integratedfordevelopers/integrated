<?php
namespace Integrated\Bundle\ContentBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM,
    Integrated\Bundle\ContentBundle\Mapping\Annotations as Content;

/**
 * Document type File
 *
 * @package Integrated\Bundle\ContentBundle\Document
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @ODM\Document(collection="content")
 * @Content\Document("File")
 */
class File extends AbstractContent
{
    /**
     * @var string
     * @ODM\String
     */
    protected $file;

    /**
     * @var string
     * @ODM\String
     */
    protected $description;

    /**
     * Get the file of the document
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set the file of the document
     *
     * @param string $file
     * @return $this
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * Get the description of the document
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the description of the document
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
}