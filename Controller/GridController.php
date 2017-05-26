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

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Integrated\Bundle\PageBundle\Document\Page\Page;
use Integrated\Bundle\PageBundle\Grid\GridFactory;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class GridController
{
    /**
     * @var GridFactory
     */
    protected $gridFactory;

    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @param GridFactory $gridFactory
     * @param DocumentManager $dm
     */
    public function __construct(GridFactory $gridFactory, DocumentManager $dm)
    {
        $this->gridFactory = $gridFactory;
        $this->dm = $dm;
    }

    /**
     * @Template
     *
     * @param Request $request
     * @return array
     */
    public function renderAction(Request $request)
    {
        $data = (array) json_decode($request->getContent(), true);
        $grid = null;

        if (isset($data['data'])) {
            $grid = $this->gridFactory->fromArray($data['data']);
        }

        return [
            'grid' => $grid,
            'edit' => true,
        ];
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function saveAction(Request $request)
    {
        // @todo security check (INTEGRATED-383)

        $data = (array) json_decode($request->getContent(), true);

        if (!isset($data['page'])) {
            return new JsonResponse(['error' => 'No page specified']);
        }

        /** @var Page $page */
        if (!$page = $this->dm->getRepository(Page::class)->find($data['page'])) {
            return new JsonResponse(['error' => 'Page not found']);
        }

        $grids = [];

        if (isset($data['grids'])) {
            foreach ($data['grids'] as $grid) {
                $grids[] = $this->gridFactory->fromArray($grid);
            }
        }

        $page->setGrids($grids);

        $this->dm->flush();

        return new JsonResponse(['success' => true]);
    }
}
