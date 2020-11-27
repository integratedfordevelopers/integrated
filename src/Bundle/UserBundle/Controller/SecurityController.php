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

use Integrated\Bundle\UserBundle\Form\Type\LoginFormType;
use Integrated\Bundle\UserBundle\Form\Type\PasswordResetType;
use Integrated\Bundle\UserBundle\Service\Password;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The login controller.
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class SecurityController extends Controller
{
    /**
     * @var Password
     */
    private $password;

    /**
     * SecurityController constructor.
     *
     * @param Password $password
     */
    public function __construct(Password $password)
    {
        $this->password = $password;
    }

    /**
     * @return Response
     */
    public function loginAction()
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('integrated_content_content_index');
        }

        $form = $this->createForm(
            LoginFormType::class,
            null,
            ['action' => $this->generateUrl('integrated_user_check')]
        );

        return $this->render('IntegratedUserBundle:security:login.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @return RedirectResponse|Response
     */
    public function passwordResetAction(Request $request)
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('integrated_content_content_index');
        }

        $form = $this->createForm(
            PasswordResetType::class,
            null,
            ['action' => $this->generateUrl('integrated_user_password_reset')]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->password->sendResetMail($form->get('email')->getData());

            $this->get('braincrafted_bootstrap.flash')->success('If your user has been found.....');

            $this->redirectToRoute('integrated_user_login');
        }

        return $this->render('IntegratedUserBundle:security:password_reset.html.twig', ['form' => $form->createView()]);
    }
}
