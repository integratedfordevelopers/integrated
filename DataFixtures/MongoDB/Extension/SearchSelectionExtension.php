<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\DataFixtures\MongoDB\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Doctrine\ODM\MongoDB\DocumentNotFoundException;

use Integrated\Bundle\ContentBundle\Document\SearchSelection\SearchSelection;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
trait SearchSelectionExtension
{
    /**
     * @return ContainerInterface
     */
    abstract public function getContainer();

    /**
     * @param string $id
     * @return SearchSelection
     * @throws DocumentNotFoundException
     */
    public function searchSelection($id)
    {
        $searchSelection = $this->getContainer()
            ->get('doctrine.odm.mongodb.document_manager')
            ->getRepository(SearchSelection::class)->find($id);

        if (!$searchSelection) {
            throw DocumentNotFoundException::documentNotFound(SearchSelection::class, $id);
        }

        return $searchSelection;
    }
}
