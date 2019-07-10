<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WebsiteBundle\Controller\Content;

use Integrated\Bundle\BlockBundle\Templating\BlockManager;
use Integrated\Bundle\ContentBundle\Document\Content\Relation\Company;
use Integrated\Bundle\PageBundle\Document\Page\ContentTypePage;
use Integrated\Bundle\ThemeBundle\Templating\ThemeManager;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class CompanyController
{
    /**
     * @var ChannelContextInterface
     */
    protected $channelContext;

    /**
     * @var TwigEngine
     */
    protected $templating;

    /**
     * @var ThemeManager
     */
    protected $themeManager;

    /**
     * @var BlockManager
     */
    protected $blockManager;

    /**
     * @param TwigEngine   $templating
     * @param ThemeManager $themeManager
     * @param BlockManager $blockManager
     */
    public function __construct(ChannelContextInterface $channelContext, TwigEngine $templating, ThemeManager $themeManager, BlockManager $blockManager)
    {
        $this->channelContext = $channelContext;
        $this->templating = $templating;
        $this->themeManager = $themeManager;
        $this->blockManager = $blockManager;
    }

    /**
     * @param ContentTypePage $page
     * @param Company         $company
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(ContentTypePage $page, Company $company)
    {
        if (!$company->isPublished() || !$company->hasChannel($this->channelContext->getChannel())) {
            throw new NotFoundHttpException();
        }

        $this->blockManager->setDocument($company);

        return $this->templating->renderResponse(
            $this->themeManager->locateTemplate('content/company/show/'.$page->getLayout()),
            [
                'company' => $company,
                'page' => $page,
            ]
        );
    }
}
