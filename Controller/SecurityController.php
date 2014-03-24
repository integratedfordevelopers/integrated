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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Configuration class for ContentBundle
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class SecurityController extends Controller
{
	public function loginAction()
	{
		$form = $this->createForm(
			$this->get('integrated_user.security.login.form.type'),
			null,
			['action' => $this->generateUrl('integrated_user_check')]
		);

		return $this->render('IntegratedUserBundle:Security:login.html.twig', ['form' => $form->createView()]);
	}
}