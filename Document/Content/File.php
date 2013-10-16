<?php
/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
namespace Integrated\Bundle\ContentBundle\Document\Content;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM,
    Integrated\Bundle\ContentBundle\Mapping\Annotations as Content;

/**
 * Document type File
 *
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