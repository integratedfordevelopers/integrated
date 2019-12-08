<?php

namespace Integrated\Bundle\WebsiteBundle\Controller;

use Integrated\Bundle\ThemeBundle\Templating\ThemeManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @var TwigEngine
     */
    protected $templating;

    /**
     * @var ThemeManager
     */
    protected $themeManager;

    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtils;

    /**
     * @param TwigEngine          $templating
     * @param ThemeManager        $themeManager
     * @param AuthenticationUtils $authenticationUtils
     */
    public function __construct(TwigEngine $templating, ThemeManager $themeManager, AuthenticationUtils $authenticationUtils)
    {
        $this->templating = $templating;
        $this->themeManager = $themeManager;
        $this->authenticationUtils = $authenticationUtils;
    }

    /**
     * @return Response
     */
    public function loginAction(): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $this->authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $this->authenticationUtils->getLastUsername();

        return $this->render($this->themeManager->locateTemplate('security/login.html.twig'), ['last_username' => $lastUsername, 'error' => $error]);
    }

    public function logout()
    {
        //This method can be blank - it will be intercepted by the firewall
    }
}
