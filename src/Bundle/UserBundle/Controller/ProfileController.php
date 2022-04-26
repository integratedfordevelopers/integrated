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

use Symfony\Component\Form\FormInterface;
use Integrated\Bundle\FormTypeBundle\Form\Type\FormActionsType;
use Integrated\Bundle\UserBundle\Form\Type\ProfileFormType;
use Integrated\Bundle\UserBundle\Model\UserInterface;
use Integrated\Bundle\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class ProfileController extends AbstractController
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @var PasswordHasherFactoryInterface
     */
    protected $hasherFactory;

    /**
     * @param UserManagerInterface           $userManager
     * @param PasswordHasherFactoryInterface $hasherFactory
     * @param ContainerInterface             $container
     */
    public function __construct(
        UserManagerInterface $userManager,
        PasswordHasherFactoryInterface $hasherFactory,
        ContainerInterface $container
    ) {
        $this->userManager = $userManager;
        $this->hasherFactory = $hasherFactory;
        $this->container = $container;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createProfileForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->get('actions')->get('cancel')->isClicked()) {
                return $this->redirectToRoute('integrated_content_content_index');
            }

            if ($form->isValid()) {
                $user->setPassword($this->hasherFactory->getPasswordHasher($user)->hash($form->get('password')->getData()));
                $user->setSalt(null);

                $this->userManager->persist($user);
                $this->addFlash('success', 'Your profile have been saved');

                return $this->redirectToRoute('integrated_content_content_index');
            }
        }

        return $this->render('@IntegratedUser/profile/index.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param UserInterface $user
     *
     * @return FormInterface
     */
    protected function createProfileForm(UserInterface $user)
    {
        $form = $this->createForm(
            ProfileFormType::class,
            $user,
            [
                'action' => $this->generateUrl('integrated_user_profile_index'),
                'method' => 'POST',
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
}
