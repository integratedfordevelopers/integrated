<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Grid;

use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Integrated\Bundle\PageBundle\Document\Page\Grid\Column;
use Integrated\Bundle\PageBundle\Document\Page\Grid\Grid;
use Integrated\Bundle\PageBundle\Document\Page\Grid\Item;
use Integrated\Bundle\PageBundle\Document\Page\Grid\Row;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class GridFactory
{
    /**
     * @var DocumentRepository
     */
    protected $blockRepository;

    /**
     * @param DocumentRepository $blockRepository
     */
    public function __construct(DocumentRepository $blockRepository)
    {
        $this->blockRepository = $blockRepository;
    }

    /**
     * @param array $array
     *
     * @return \Integrated\Bundle\PageBundle\Document\Page\Grid\Grid|null
     */
    public function fromArray(array $array = [])
    {
        if (isset($array['id'])) {
            $grid = new Grid($array['id']);

            if (isset($array['items'])) {
                $grid->setItems($this->parseItems((array) $array['items']));
            }

            return $grid;
        }
    }

    /**
     * @param array $array
     *
     * @return array
     */
    protected function parseItems(array $array = [])
    {
        $items = [];

        foreach ($array as $value) {
            $item = new Item();

            if (isset($value['order'])) {
                $item->setOrder($value['order']);
            }

            if (isset($value['block'])) {
                $item->setBlock($this->blockRepository->find($value['block']));
            }

            if (isset($value['row'])) {
                $row = new Row();

                if (isset($value['row']['columns'])) {
                    foreach ((array) $value['row']['columns'] as $data) {
                        $column = new Column();

                        if (isset($data['size'])) {
                            $column->setSize($data['size']);
                        }

                        if (isset($data['items'])) {
                            $column->setItems($this->parseItems((array) $data['items']));
                        }

                        $row->addColumn($column);
                    }

                    if (\count($row->getColumns())) {
                        $item->setRow($row);
                    }
                }
            }

            $items[] = $item;
        }

        return $items;
    }
}
