<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Services;

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Bundle\PageBundle\Document\Page\ContentTypePage;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class ContentTypePageService
{
    /**
     * @var ContentTypeControllerManager
     */
    protected $controllerManager;

    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @param ContentTypeControllerManager $controllerManager
     * @param DocumentManager              $dm
     */
    public function __construct(ContentTypeControllerManager $controllerManager, DocumentManager $dm)
    {
        $this->controllerManager = $controllerManager;
        $this->dm = $dm;
    }

    /**
     * @param ContentType $contentType
     * @param Channel     $channel
     */
    public function addContentType(ContentType $contentType, Channel $channel)
    {
        $controller = $this->controllerManager->getController($contentType->getClass());

        // don't add if no controller service is defined
        if (!\is_array($controller)) {
            return;
        }

        $contentTypePage = new ContentTypePage($contentType, $channel);

        $contentTypePage->setControllerService($controller['service']);
        $contentTypePage->setControllerAction($controller['controller_actions'][0]);

        $this->dm->persist($contentTypePage);
        $this->dm->flush();
    }
}
