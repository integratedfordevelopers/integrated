<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ThemeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Bundle\ThemeBundle\Entity\Scraper\Block;

class Scraper
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $channelId;

    /**
     * @var string
     */
    private $templateName;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $template;

    /**
     * @var int
     */
    private $lastModified;

    /**
     * @var string
     */
    private $lastError;

    /**
     * @var ArrayCollection
     */
    private $blocks;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->lastModified = time();
        $this->blocks = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getChannelId(): ?string
    {
        return $this->channelId;
    }

    /**
     * @param string $channelId
     */
    public function setChannelId(string $channelId): void
    {
        $this->channelId = $channelId;
    }

    /**
     * @return string
     */
    public function getTemplateName(): ?string
    {
        return $this->templateName;
    }

    /**
     * @param string $templateName
     */
    public function setTemplateName(string $templateName): void
    {
        $this->templateName = $templateName;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     */
    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string|null
     */
    public function getTemplate(): ?string
    {
        return $this->template;
    }

    /**
     * @param string|null $template
     */
    public function setTemplate(?string $template): void
    {
        $this->template = $template;
    }

    /**
     * @return int
     */
    public function getLastModified(): int
    {
        return $this->lastModified;
    }

    /**
     * @param int $lastModified
     */
    public function setLastModified(int $lastModified): void
    {
        $this->lastModified = $lastModified;
    }

    /**
     * @return string|null
     */
    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    /**
     * @param string $lastError
     */
    public function setLastError(?string $lastError = null): void
    {
        $this->lastError = \is_string($lastError) ? substr($lastError, 0, 800) : null;
    }

    /**
     * @return Block[]
     */
    public function getBlocks(): array
    {
        return $this->blocks->toArray();
    }

    /**
     * @param Block[] $blocks
     */
    public function setBlocks(array $blocks): void
    {
        $this->blocks = new ArrayCollection($blocks);
    }

    /**
     * @param Block $block
     */
    public function addBlock(Block $block): void
    {
        if (!$this->blocks->contains($block)) {
            $this->blocks->add($block);
        }
    }

    /**
     * @param Block $block
     *
     * @return bool
     */
    public function hasBlock(Block $block): bool
    {
        return $this->blocks->contains($block);
    }

    /**
     * @param Block $block
     */
    public function removeBlock(Block $block): void
    {
        $this->blocks->removeElement($block);
    }
}
