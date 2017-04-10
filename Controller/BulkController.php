<?php
/**
 * Created by PhpStorm.
 * User: Patrick
 * Date: 21/03/2017
 * Time: 12:36
 */

namespace Integrated\Bundle\ContentBundle\Controller;

use Integrated\Bundle\ContentBundle\Form\Type\BulkActionFormType;
use Integrated\Bundle\ContentBundle\Form\Type\BulkSelectionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class BulkController extends Controller
{
    /**
     * @Template()
     * @param Request $request
     * @return array
     */
    public function selectAction(Request $request)
    {
        // Fetch Content selection.
        $bulkSelector = $this->get('integrated_content.services.bulk.selector');
        $selectionLimit = $this->container->getParameter('bulk_action_limit');
        $results = $bulkSelector->selection($request, $selectionLimit);

        $form = $this->createSelectForm($results);
        $form->handleRequest($request);

        // Get selection, save it in session and redirect when form is valid.
        if ($form->isSubmitted() && $form->isValid()) {
            $selection = $form->getData();
            $session = $request->getSession();
            $session->set('selection', $selection);

            return $this->redirectToRoute('integrated_content_bulk_edit', $request ? $request->query->all() : []);
        }

        return [
            'filters' => $request ? $request->query->all() : [],
            'results' => $results,
            'limit' => $selectionLimit,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Template()
     * @param Request $request
     * @return array
     */
    public function editAction(Request $request)
    {
        $session = $request->getSession();
        $selection = $session->get('selection');

        // Get all possible relations.
        $bulkAction = $this->get('integrated_content.services.bulk.action');
        $relations = $bulkAction->getAllRelations();

        $form = $this->createBulkActionForm($relations);
        $form->handleRequest($request);

        // Set references in session and redirect if form is valid.
        if ($form->isSubmitted() && $form->isValid()) {
            $references = $form->getData();
            $session->set('references', $references);

            return $this->redirectToRoute('integrated_content_bulk_confirm', $request ? $request->query->all() : []);
        }

        return [
            'filters' => $request ? $request->query->all() : [],
            'selection' => $selection['selection'],
            'form' => $form->createView(),
        ];
    }

    /**
     * @Template()
     * @param Request $request
     * @return array
     */
    public function confirmAction(Request $request)
    {
        $session = $request->getSession();
        $selection = $session->get('selection');
        $references = $session->get('references');

        $bulkAction = $this->get('integrated_content.services.bulk.action');

        $form = $this->createConfirmForm();
        $form->handleRequest($request);

        // If form submitted execute bulk action.
        if ($form->isSubmitted() && $form->isValid()) {
            if ($session instanceof Session) {
                $executed = $bulkAction->execute($selection['selection'], $references, $session);

                // If bulk actions was completed successfully redirect to indexpage.
                if ($executed) {
                    return $this->redirectToRoute('integrated_content_content_index', $request ? $request->query->all() : []);
                }
            }
        } else {
            $number = count($selection['selection']);

            $this->addFlash(
                'message',
                "Please confirm that you wish to " . $bulkAction->actionsToString($references, " the following ") . " for the selected " . ($number > 1 ? "$number contents." : "content.")
            );
        }

        return [
            'filters' => $request ? $request->query->all() : [],
            'referencegroups' => $bulkAction->getReferenceNames($references),
            'form' => $form->createView(),
            'delimiter' => $bulkAction::BULK_DELIMITER,
        ];
    }

    /**
     * Shows the menu.
     * @Template
     */
    public function menuAction()
    {
        /** @var Request $request */
        $request = $this->get('request_stack')->getMasterRequest();

        return [
            'filters' => $request ? $request->query->all() : []
        ];
    }

    /**
     * Creating form for bulk-selection.
     * @param $results
     * @return \Symfony\Component\Form\Form
     */
    protected function createSelectForm($results)
    {

        $selection = [];

        foreach ($results as $result) {
            $selection[$result['title']] = $result['type_id'];
        }

        $form = $this->createForm(BulkSelectionType::class, $empty = [], ['selection' => $selection])
            ->add('save', SubmitType::class, [
                'label' => 'Next',
                'attr' => ['class' => 'btn-orange']
            ])
            ->add('saveAndAdd', SubmitType::class, [
                'label' => 'Next',
                'attr' => ['class' => 'btn-orange']
            ]);

        return $form;
    }

    /**
     * Creating form for bulk-action.
     * @param $relations
     * @return \Symfony\Component\Form\Form
     */
    protected function createBulkActionForm($relations)
    {
        $form = $this->createForm(BulkActionFormType::class, $empty = [], ['relations' => $relations])
            ->add('save', SubmitType::class, [
                'label' => 'Submit',
                'attr' => [
                    'class' => 'btn-orange',
                    'style' => 'display: none'
                ]
            ]);

        return $form;
    }

    /**
     * Creating form for bulk-confirmation.
     * @return \Symfony\Component\Form\Form
     */
    protected function createConfirmForm()
    {
        return $this->createFormBuilder($empty = [])
            ->add('confirm', SubmitType::class, [
                'label' => 'Confirm',
                'attr' => ['class' => 'btn-orange']
            ])
            ->getForm();
    }
}
