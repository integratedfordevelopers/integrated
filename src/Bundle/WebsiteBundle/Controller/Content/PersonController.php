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

use Integrated\Bundle\ContentBundle\Document\Content\Relation\Person;
use Integrated\Bundle\PageBundle\Document\Page\ContentTypePage;
use Integrated\Bundle\ThemeBundle\Exception\CircularFallbackException;
use Integrated\Bundle\ThemeBundle\Templating\ThemeManager;
use Integrated\Bundle\WebsiteBundle\Service\ContentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Twig\Error\Error;

class PersonController extends AbstractController
{
    /**
     * @var ContentService
     */
    private $contentService;

    /**
     * @var ThemeManager
     */
    protected $themeManager;

    /**
     * @param ContentService $contentService
     * @param ThemeManager   $themeManager
     */
    public function __construct(ContentService $contentService, ThemeManager $themeManager)
    {
        $this->contentService = $contentService;
        $this->themeManager = $themeManager;
    }

    /**
     * @param ContentTypePage $page
     * @param Person          $person
     *
     * @return Response
     *
     * @throws CircularFallbackException
     * @throws Error
     */
    public function showAction(ContentTypePage $page, Person $person)
    {
        $this->contentService->prepare($person);

        return $this->render(
            $this->themeManager->locateTemplate('content/person/show/'.$page->getLayout()),
            [
                'person' => $person,
                'page' => $page,
            ]
        );
    }
}
