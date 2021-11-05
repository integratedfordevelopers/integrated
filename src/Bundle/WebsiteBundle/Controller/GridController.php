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

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Integrated\Bundle\PageBundle\Document\Page\AbstractPage;
use Integrated\Bundle\PageBundle\Document\Page\Grid\Grid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class GridController extends AbstractController
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function saveAction(Request $request)
    {
        if (!$this->isGranted('ROLE_WEBSITE_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $data = (array) json_decode($request->getContent(), true);

        if (!isset($data['page'])) {
            return new JsonResponse(['error' => 'No page specified']);
        }

        $dm = $this->get('doctrine_mongodb')->getManager();

        /** @var AbstractPage $page */
        if (!$page = $dm->getRepository(AbstractPage::class)->find($data['page'])) {
            return new JsonResponse(['error' => 'Page not found']);
        }

        $grids = [];

        if (isset($data['grids'])) {
            foreach ($data['grids'] as $grid) {
                $grid = $this->get('integrated_page.grid.factory')->fromArray($grid);

                if ($grid instanceof Grid) {
                    $grids[] = $grid;
                }
            }
        }

        $page->setGrids($grids);

        $dm->flush();

        return new JsonResponse(['success' => true]);
    }
}
