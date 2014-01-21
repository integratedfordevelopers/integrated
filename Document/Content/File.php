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

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Integrated\Common\ContentType\Mapping\Annotations as Type;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Document type File
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 *
 * @ODM\Document
 * @Type\Document("File")
 */
class File extends Content
{
	/**
	 * @var string
	 */
	private $temp;

	/**
	 * @var UploadFile
	 * @Type\Field(type="file")
	 */
	protected $file;

	/**
	 * @var string
	 * @ODM\Field(type="string", name="file")
	 */
	protected $path;

	/**
	 * @var string
	 * @ODM\String
	 * @Type\Field
	 */
	protected $title;

	/**
	 * @var string
	 * @ODM\String
	 * @Type\Field
	 */
	protected $description;

	/**
	 * Set the file of the document
	 *
	 * @param UploadedFile $file
	 * @return $this
	 */
	public function setFile(UploadedFile $file = null)
	{
		$this->file = $file;

		// Check if there is an old file and store it in temp so it can be deleted after update
		if (is_file($this->getAbsolutePath())) {
			$this->temp = $this->getAbsolutePath();
		} else {
			$this->path = 'initial';
		}

		return $this;
	}

	/**
	 * Get the file of the document
	 *
	 * @return UploadedFile
	 */
	public function getFile()
	{
		return $this->file;
	}

	/**
	 * Get the absolute path where uploaded files should be saved
	 *
	 * @return string
	 */
	protected function getUploadRootDir()
	{
		return __DIR__ . '/../../../../../../web/' . $this->getUploadDir();
	}

	/**
	 * Get the upload dir for displaying uploaded files in the view
	 *
	 * @return string
	 */
	protected function getUploadDir()
	{
		return 'uploads/documents';
	}

	/**
	 * Get the absolute path of an uploaded file
	 *
	 * @return null|string
	 */
	public function getAbsolutePath()
	{
		return null === $this->path ? null : $this->getUploadRootDir() . '/' . $this->id . '.' . $this->path;
	}

	/**
	 * Get the public/web path of an uploaded file
	 *
	 * @return null|string
	 */
	public function getWebPath()
	{
		return null === $this->path ? null : $this->getUploadDir() . '/' . $this->path;
	}

	/**
	 * @ODM\PrePersist()
	 * @ODM\PreUpdate()
	 */
	public function preUpload()
	{
		if (null !== $this->getFile()) {
			$this->path = $this->getFile()->guessExtension();
		}
	}

	/**
	 * @ODM\PostPersist()
	 * @ODM\PostUpdate()
	 */
	public function upload()
	{
		// Only upload if we got a file
		if (null === $this->getFile()) {
			return;
		}

		// Remove old images
		if (isset($this->temp)) {
			unlink($this->temp);
			$this->temp = null;
		}

		// Set path
		$this->path = $this->getFile()->guessExtension();

		// Move file
		$this->getFile()->move($this->getUploadRootDir(), $this->id . '.' . $this->path);

		// Unset file
		$this->setFile(null);
	}

	/**
	 * @ODM\PreRemove()
	 */
	public function storeFilenameForRemove()
	{
		$this->temp = $this->getAbsolutePath();
	}

	/**
	 * @ODM\PostRemove()
	 */
	public function removeUpload()
	{
		if (isset($this->temp)) {
			unlink($this->temp);
			$this->temp = null;
		}
	}

	/**
	 * Get the title of the document
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Set the title of the document
	 *
	 * @param string $title
	 * @return $this
	 */
	public function setTitle($title)
	{
		$this->title = $title;
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