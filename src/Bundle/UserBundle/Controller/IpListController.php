<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Controller;

use Integrated\Bundle\FormTypeBundle\Form\Type\FormActionsType;
use Symfony\Component\Form\FormInterface;
use Integrated\Bundle\IntegratedBundle\Controller\AbstractController;
use Integrated\Bundle\UserBundle\Form\Type\DeleteFormType;
use Integrated\Bundle\UserBundle\Form\Type\IpListFormType;
use Integrated\Bundle\UserBundle\Model\IpList;
use Integrated\Bundle\UserBundle\Model\IpListManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IpListController extends AbstractController
{
    /**
     * @var IpListManagerInterface
     */
    private $manager;

    public function __construct(IpListManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $paginator = $this->getPaginator()->paginate(
            $this->manager->findAll(),
            $request->query->get('page', 1),
            15
        );

        return $this->render('@IntegratedUser/ip_list/index.html.twig', [
            'lists' => $paginator,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createNewForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->get('actions')->get('cancel')->isClicked()) {
                return $this->redirectToRoute('integrated_user_iplist_index');
            }

            if ($form->isValid()) {
                $list = $form->getData();

                $this->manager->persist($list);

                $this->addFlash('success', sprintf(
                    'Added the ip %s to the whitelist',
                    $list->getIp()->getProtocolAppropriateAddress()
                ));

                return $this->redirectToRoute('integrated_user_iplist_index');
            }
        }

        return $this->render('@IntegratedUser/ip_list/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param IpList  $list
     * @param Request $request
     *
     * @return Response
     */
    public function edit(IpList $list, Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createEditForm($list);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->get('actions')->get('cancel')->isClicked()) {
                return $this->redirectToRoute('integrated_user_iplist_index');
            }

            if ($form->isValid()) {
                $this->manager->persist($list);

                $this->addFlash('success', sprintf(
                    'The changes to the ip %s are saved',
                    $list->getIp()->getProtocolAppropriateAddress()
                ));

                return $this->redirectToRoute('integrated_user_iplist_index');
            }
        }

        return $this->render('@IntegratedUser/ip_list/edit.html.twig', [
            'list' => $list,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param IpList  $list
     * @param Request $request
     *
     * @return Response
     */
    public function delete(IpList $list, Request $request)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createDeleteForm($list);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->get('actions')->get('cancel')->isClicked()) {
                return $this->redirectToRoute('integrated_user_iplist_index');
            }

            if ($form->isValid()) {
                $this->manager->remove($list);

                $this->addFlash('success', sprintf(
                    'The ip %s is removed from the whitelist',
                    $list->getIp()->getProtocolAppropriateAddress()
                ));

                return $this->redirectToRoute('integrated_user_iplist_index');
            }
        }

        return $this->render('@IntegratedUser/ip_list/delete.html.twig', [
            'list' => $list,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return FormInterface
     */
    protected function createNewForm()
    {
        $form = $this->createForm(
            IpListFormType::class,
            null,
            [
                'action' => $this->generateUrl('integrated_user_iplist_new'),
                'method' => 'POST',
            ]
        );

        $form->add('actions', FormActionsType::class, [
            'buttons' => [
                'create' => ['type' => SubmitType::class, 'options' => ['label' => 'Create']],
                'cancel' => ['type' => SubmitType::class, 'options' => ['label' => 'Cancel', 'attr' => ['type' => 'default', 'formnovalidate' => true]]],
            ],
        ]);

        return $form;
    }

    /**
     * @param IpList $list
     *
     * @return FormInterface
     */
    protected function createEditForm(IpList $list)
    {
        $form = $this->createForm(
            IpListFormType::class,
            $list,
            [
                'action' => $this->generateUrl('integrated_user_iplist_edit', ['id' => $list->getId()]),
                'method' => 'PUT',
            ]
        );

        $form->add('actions', FormActionsType::class, [
            'buttons' => [
                'save' => ['type' => SubmitType::class, 'options' => ['label' => 'Save']],
                'cancel' => ['type' => SubmitType::class, 'options' => ['label' => 'Cancel', 'attr' => ['type' => 'default', 'formnovalidate' => true]]],
            ],
        ]);

        return $form;
    }

    /**
     * @param IpList $list
     *
     * @return FormInterface
     */
    protected function createDeleteForm(IpList $list)
    {
        $form = $this->createForm(
            DeleteFormType::class,
            $list,
            [
                'action' => $this->generateUrl('integrated_user_iplist_delete', ['id' => $list->getId()]),
                'method' => 'DELETE',
            ]
        );

        $form->add('actions', FormActionsType::class, [
            'buttons' => [
                'delete' => ['type' => SubmitType::class, 'options' => ['label' => 'Delete']],
                'cancel' => ['type' => SubmitType::class, 'options' => ['label' => 'Cancel', 'attr' => ['type' => 'default', 'formnovalidate' => true]]],
            ],
        ]);

        return $form;
    }
}
