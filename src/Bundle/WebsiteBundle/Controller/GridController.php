<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WebsiteBundle\Controller;

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\PageBundle\Document\Page\AbstractPage;
use Integrated\Bundle\PageBundle\Document\Page\Grid\Grid;
use Integrated\Bundle\PageBundle\Grid\GridFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class GridController extends AbstractController
{
    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var GridFactory
     */
    private $gridFactory;

    public function __construct(DocumentManager $documentManager, GridFactory $gridFactory)
    {
        $this->documentManager = $documentManager;
        $this->gridFactory = $gridFactory;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function save(Request $request)
    {
        if (!$this->isGranted('ROLE_WEBSITE_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $data = (array) json_decode($request->getContent(), true);

        if (!isset($data['page'])) {
            return new JsonResponse(['error' => 'No page specified']);
        }

        /** @var AbstractPage $page */
        if (!$page = $this->documentManager->getRepository(AbstractPage::class)->find($data['page'])) {
            return new JsonResponse(['error' => 'Page not found']);
        }

        $grids = [];

        if (isset($data['grids'])) {
            foreach ($data['grids'] as $grid) {
                $grid = $this->gridFactory->fromArray($grid);

                if ($grid instanceof Grid) {
                    $grids[] = $grid;
                }
            }
        }

        $page->setGrids($grids);

        $this->documentManager->flush();

        return new JsonResponse(['success' => true]);
    }
}
