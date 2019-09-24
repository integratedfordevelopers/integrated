<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Controller;

use Doctrine\ODM\MongoDB\DocumentManager;
use Exception;
use Integrated\Bundle\ContentBundle\Document\Bulk\BulkAction;
use Integrated\Bundle\ContentBundle\Form\Type\BulkActionConfirmType;
use Integrated\Bundle\ContentBundle\Form\Type\BulkConfigureType;
use Integrated\Bundle\ContentBundle\Form\Type\BulkSelectionType;
use Integrated\Bundle\ContentBundle\Provider\ContentProvider;
use Integrated\Common\Bulk\BulkHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RankController extends Controller
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var ContentProvider
     */
    protected $contentProvider;

    /**
     * @var BulkHandlerInterface
     */
    protected $bulkHandler;

    /**
     * @param DocumentManager      $dm
     * @param ContentProvider      $contentProvider
     */
    public function __construct(
        DocumentManager $dm,
        ContentProvider $contentProvider
    ) {
        $this->dm = $dm;
        $this->contentProvider = $contentProvider;
    }

    /**
     * @param Request    $request
     * @param BulkAction $bulk
     *
     * @return RedirectResponse|Response
     */
    public function lookup(Request $request)
    {
        $limit = 20;

        $request->query->set('sort', 'rank');

        $content = $this->contentProvider->getContentFromSolr($request, $limit);

        return $this->render('IntegratedContentBundle:rank:lookup.json.twig', [
            'pager' => $content,
            'relations' => [],
        ]);
    }

}
