<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Document\Page;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Bundle\ContentBundle\Document\Channel\Channel;

/**
 * @author Johan Liefers <johan@e-active.nl>
 *
 * @ODM\Document(collection="content_type_page")
 */
class ContentTypePage extends Page
{
    /**
     * @var string
     * @ODM\Id(strategy="UUID")
     */
    protected $id;

    /**
     * @var string
     * @ODM\String
     * @Assert\NotBlank
     * @Assert\Regex(
     *     pattern="/{slug}/",
     *     message="Url must contain {slug}"
     * )
     */
    protected $path;

    /**
     * @var string
     * @ODM\String
     * @Assert\NotBlank
     */
    protected $controllerService;

    /**
     * @var string
     * @ODM\String
     * @Assert\NotBlank
     */
    protected $controllerAction;

    /**
     * @var ContentType
     * @ODM\ReferenceOne(targetDocument="Integrated\Bundle\ContentBundle\Document\ContentType\ContentType")
     */
    protected $contentType;

    /**
     * @param ContentType $contentType
     * @param Channel $channel
     * @param string $layout
     * @param null $path
     */
    public function __construct(ContentType $contentType, Channel $channel, $layout = 'default.html.twig', $path = null)
    {
        parent::__construct();

        $this->setContentType($contentType);
        $this->setChannel($channel);
        $this->setLayout($layout);
        if ($path) {
            $this->setPath($path);
        } else {
            $this->setPath(sprintf('/content/%s/{slug}', $contentType->getId()));
        }
    }

    /**
     * @return ContentType
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param ContentType $contentType
     * @return $this
     */
    public function setContentType(ContentType $contentType)
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * @return string
     */
    public function getControllerService()
    {
        return $this->controllerService;
    }

    /**
     * @param string $controllerService
     */
    public function setControllerService($controllerService)
    {
        $this->controllerService = $controllerService;
    }

    /**
     * @return string
     */
    public function getControllerAction()
    {
        return $this->controllerAction;
    }

    /**
     * @param string $controllerAction
     */
    public function setControllerAction($controllerAction)
    {
        $this->controllerAction = $controllerAction;
    }
}
