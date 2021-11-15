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

use Integrated\Bundle\ContentBundle\Document\Block\ContentBlock;
use Integrated\Bundle\ContentBundle\Document\SearchSelection\SearchSelection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class SearchSelectionController extends AbstractController
{
    /**
     * @Template
     *
     * @param Request         $request
     * @param SearchSelection $selection
     *
     * @return array
     */
    public function rssAction(Request $request, SearchSelection $selection)
    {
        $block = new ContentBlock();
        $block->setSearchSelection($selection);

        if ($itemsPerPage = $request->query->get('itemsPerPage')) {
            $itemsPerPage = ($itemsPerPage > 500) ? 500 : $itemsPerPage;
            $block->setItemsPerPage($itemsPerPage);
        }

        return $this->render('IntegratedWebsiteBundle:search_selection:rss.'.$request->getRequestFormat('xml').'.twig', [
            'selection' => $selection,
            'documents' => $this->get('integrated_content.provider.solarium')->execute($block, $request),
        ]);
    }
}
