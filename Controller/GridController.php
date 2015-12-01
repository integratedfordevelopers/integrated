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

use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Integrated\Bundle\PageBundle\Grid\GridFactory;
use Integrated\Bundle\PageBundle\Document\Page\Grid\Grid;

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
     * @param GridFactory $gridFactory
     */
    public function __construct(GridFactory $gridFactory)
    {
        $this->gridFactory = $gridFactory;
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

            if ($grid instanceof Grid) {
                $this->prepareGrid($grid);
            }
        }

        return [
            'grid' => $grid
        ];
    }

    /**
     * @param Grid $grid
     */
    protected function prepareGrid(Grid $grid)
    {
        foreach ($grid->getItems() as $item) {
            $item->setAttribute('data-json', json_encode($item->toArray(false)));
        }
    }
}
