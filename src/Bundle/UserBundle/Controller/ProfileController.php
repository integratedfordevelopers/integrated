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

use Braincrafted\Bundle\BootstrapBundle\Form\Type\FormActionsType;
use Integrated\Bundle\UserBundle\Form\Type\ProfileFormType;
use Integrated\Bundle\UserBundle\Model\UserInterface;
use Integrated\Bundle\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createProfileForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->get('actions')->get('cancel')->isClicked()) {
                return $this->redirect($this->generateUrl('integrated_content_content_index'));
            }

            if ($form->isValid()) {
                $salt = base64_encode(random_bytes(72));

                $user->setPassword($this->getEncoder($user)->encodePassword($form->get('password')->getData(), $salt));
                $user->setSalt($salt);

                $this->getManager()->persist($user);
                $this->get('braincrafted_bootstrap.flash')->success('Your profile have been saved');

                return $this->redirect($this->generateUrl('integrated_content_content_index'));
            }
        }

        return $this->render('IntegratedUserBundle:profile:index.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param UserInterface $user
     *
     * @return \Symfony\Component\Form\FormInterface
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
                'cancel' => ['type' => SubmitType::class, 'options' => ['label' => 'Cancel', 'attr' => ['type' => 'default']]],
            ],
        ]);

        return $form;
    }

    /**
     * @return UserManagerInterface
     */
    protected function getManager()
    {
        return $this->container->get('integrated_user.user.manager');
    }

    /**
     * @param UserInterface $user
     *
     * @return \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface
     */
    protected function getEncoder(UserInterface $user)
    {
        return $this->container->get('security.encoder_factory')->getEncoder($user);
    }
}
