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
use Integrated\Bundle\ContentBundle\Form\Type\DeleteFormType;
use Integrated\Bundle\ThemeBundle\Entity\Scraper;
use Integrated\Bundle\ThemeBundle\Form\Type\ScraperType;
use Integrated\Bundle\ThemeBundle\Scraper\Scraper as ScraperService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ScraperController extends AbstractController
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
     * Lists all the Scrapers.
     *
     * @return Response
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN']);

        $result = $this->entityManager->getRepository(Scraper::class)->findBy([], ['channelId' => 'asc', 'name' => 'asc']);

        return $this->render('@IntegratedTheme/scraper/index.html.twig', [
            'result' => $result,
        ]);
    }

    /**
     * Creates a new Scraper.
     *
     * @param Request $request
     *
     * @return Response|RedirectResponse
     */
    public function new(Request $request): Response
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

            return $this->redirectToRoute('integrated_theme_scraper_edit', ['id' => $scraper->getId()]);
        }

        return $this->render('@IntegratedTheme/scraper/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edits an existing Scraper.
     *
     * @param Scraper $scraper
     * @param Request $request
     *
     * @return Response|RedirectResponse
     */
    public function edit(Scraper $scraper, Request $request): Response
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN']);

        $form = $this->createForm(ScraperType::class, $scraper);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->scraper->prepare($scraper);

            $this->get('braincrafted_bootstrap.flash')->success('Item updated');

            return $this->redirectToRoute('integrated_theme_scraper_index');
        }

        return $this->render('@IntegratedTheme/scraper/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a Scraper.
     *
     * @param Scraper $scraper
     * @param Request $request
     *
     * @return Response
     */
    public function deleteAction(Scraper $scraper, Request $request): Response
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN']);

        $form = $this->createDeleteForm($scraper);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->remove($scraper);
            $this->entityManager->flush();

            // Set flash message
            $this->get('braincrafted_bootstrap.flash')->success('Item deleted');

            return $this->redirectToRoute('integrated_theme_scraper_index');
        }

        return $this->render('@IntegratedTheme/scraper/delete.html.twig', [
            'scraper' => $scraper,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Creates a form to delete a Scraper.
     *
     * @param Scraper $scraper
     *
     * @return Form
     */
    protected function createDeleteForm(Scraper $scraper): Form
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
