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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * The login controller.
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class SecurityController extends Controller
{
    /**
     * @return Response
     */
    public function loginAction()
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY') ){
            return $this->redirectToRoute('integrated_content_content_index');
        }
        
        $form = $this->createForm(
            LoginFormType::class,
            null,
            ['action' => $this->generateUrl('integrated_user_check')]
        );

        return $this->render('IntegratedUserBundle:security:login.html.twig', ['form' => $form->createView()]);
    }
}
