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
use Integrated\Bundle\ContentBundle\Document\Bulk\BulkAction;
use Integrated\Bundle\ContentBundle\Provider\ContentProvider;
use Integrated\Common\Bulk\BulkHandlerInterface;
use Integrated\Common\Content\RankableInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class RankController extends AbstractController
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
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param DocumentManager $dm
     * @param ContentProvider $contentProvider
     */
    public function __construct(
        DocumentManager $dm,
        ContentProvider $contentProvider,
        TranslatorInterface $translator
    ) {
        $this->dm = $dm;
        $this->contentProvider = $contentProvider;
        $this->translator = $translator;
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
        $current = $request->get('current');

        $request->query->set('sort', 'rank');
        $request->query->set('hasFields', ['rank']);

        $content = $this->contentProvider->getContentFromSolr($request, $limit);

        $found = false;
        $skipItem = false;
        $skipItemName = false;
        $previous = '-first-';
        $previousName = $this->translator->trans('First item');
        foreach ($content as $item) {
            if ($item instanceof RankableInterface) {
                if ($item->getRank() == $current) {
                    $skipItem = $previous;
                    $skipItemName = $previousName;
                    $found = true;
                }
            }
            $previous = $item->getRank();
            $previousName = $this->translator->trans('After').' '.(string) $item;
        }

        $result = [];
        if ($skipItem != '-first-') {
            $result['-first-'] = $this->translator->trans('First item');
        }
        foreach ($content as $item) {
            if ($skipItem != $item->getRank()) {
                $result[$item->getRank()] = ($current == $item->getRank())
                    ? $this->translator->trans('Current position').' ('.$skipItemName.')'
                    : $this->translator->trans('After').' '.(string) $item;
            }
        }
        if (!$found) {
            $result[$current] = '...'.$this->translator->trans('Current position');
        }

        return $this->render('IntegratedContentBundle:rank:lookup.json.twig', [
            'result' => $result,
            'relations' => [],
        ]);
    }
}
