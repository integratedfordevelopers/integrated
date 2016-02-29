<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Twig\Extension;

use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Bundle\PageBundle\ContentType\ContentTypeControllerManager;
use Integrated\Bundle\PageBundle\Services\UrlResolver;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use Integrated\Common\Content\ContentInterface;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class UrlExtension extends \Twig_Extension
{
    /**
     * @var UrlResolver
     */
    protected $urlResolver;

    /**
     * UrlExtension constructor.
     * @param UrlResolver $urlResolver
     */
    public function __construct(UrlResolver $urlResolver)
    {
        $this->urlResolver = $urlResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('integrated_url', [$this->urlResolver, 'generateUrl']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_page_url';
    }
}
