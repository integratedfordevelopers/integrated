<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ThemeBundle\Controller;

use Braincrafted\Bundle\BootstrapBundle\Form\Type\FormActionsType;
use Doctrine\ORM\EntityManagerInterface;
use Integrated\Bundle\ContentBundle\Doctrine\ContentTypeManager;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Bundle\ContentBundle\Form\Type\ContentTypeFormType;
use Integrated\Bundle\ContentBundle\Form\Type\DeleteFormType;
use Integrated\Bundle\ThemeBundle\Entity\Scraper;
use Integrated\Bundle\ThemeBundle\Form\Type\ScraperType;
use Integrated\Bundle\ThemeBundle\Scraper\Scraper as ScraperService;
use Integrated\Common\ContentType\Event\ContentTypeEvent;
use Integrated\Common\ContentType\Events;
use Integrated\Common\Form\Mapping\MetadataFactory;
use Integrated\Common\Form\Mapping\MetadataFactoryInterface;
use Integrated\Common\Form\Mapping\MetadataInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ScraperController extends Controller
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ScraperService
     */
    private $scraper;

    /**
     * @param EntityManagerInterface $entityManager
     * @param ScraperService         $scraper
     */
    public function __construct(EntityManagerInterface $entityManager, ScraperService $scraper)
    {
        $this->entityManager = $entityManager;
        $this->scraper = $scraper;
    }

    /**
     * Lists all the Scrapers
     *
     * @return Response
     */
    public function indexAction()
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN']);

        $result = $this->entityManager->getRepository(Scraper::class)->findBy([], ['channelId' => 'asc', 'name' => 'asc']);

        return $this->render('IntegratedThemeBundle:scraper:index.html.twig', [
            'result' => $result,
        ]);
    }

    /**
     * Creates a new Scraper
     *
     * @param Request $request
     *
     * @return Response|RedirectResponse
     */
    public function newAction(Request $request)
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN']);

        $scraper = new Scraper();

        $form = $this->createForm(ScraperType::class, $scraper);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($scraper);
            $this->entityManager->flush();

            $this->scraper->prepare($scraper);

            $this->get('braincrafted_bootstrap.flash')->success('Item created');

            return $this->redirect($this->generateUrl('integrated_theme_scraper_edit', ['id' => $scraper->getId()]));
        }

        return $this->render('IntegratedThemeBundle:scraper:new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edits an existing Scraper
     *
     * @param Scraper $scraper
     * @param Request $request
     *
     * @return Response|RedirectResponse
     */
    public function editAction(Scraper $scraper, Request $request)
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN']);

        $form = $this->createForm(ScraperType::class, $scraper);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->scraper->prepare($scraper);

            $this->get('braincrafted_bootstrap.flash')->success('Item updated');

            return $this->redirect($this->generateUrl('integrated_theme_scraper_index'));
        }

        return $this->render('IntegratedThemeBundle:scraper:edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a Scraper
     *
     * @param Scraper $scraper
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteAction(Scraper $scraper, Request $request)
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN']);

        $form = $this->createDeleteForm($scraper);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->remove($scraper);
            $this->entityManager->flush();

            // Set flash message
            $this->get('braincrafted_bootstrap.flash')->success('Item deleted');

            return $this->redirect($this->generateUrl('integrated_theme_scraper_index'));
        }

        return $this->render('IntegratedThemeBundle:scraper:delete.html.twig', [
            'scraper' => $scraper,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Creates a form to delete a Scraper
     *
     * @param Scraper $scraper
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createDeleteForm(Scraper $scraper)
    {
        $form = $this->createForm(
            DeleteFormType::class,
            $scraper,
            [
                'action' => $this->generateUrl('integrated_theme_scraper_delete', ['id' => $scraper->getId()]),
                'method' => 'DELETE',
            ]
        );

        $form->add('actions', FormActionsType::class, [
            'buttons' => [
                'delete' => ['type' => SubmitType::class, 'options' => ['label' => 'Delete', 'attr' => ['class' => 'btn-danger']]],
            ],
        ]);

        return $form;
    }
}
