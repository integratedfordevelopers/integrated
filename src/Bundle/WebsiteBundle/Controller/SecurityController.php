<?php

namespace Integrated\Bundle\WebsiteBundle\Controller;

use Integrated\Bundle\ThemeBundle\Exception\CircularFallbackException;
use Integrated\Bundle\ThemeBundle\Templating\ThemeManager;
use Integrated\Bundle\WebsiteBundle\Security\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
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
     * @var UserManager
     */
    private $userManager;

    /**
     * @param TwigEngine          $templating
     * @param ThemeManager        $themeManager
     * @param AuthenticationUtils $authenticationUtils
     * @param UserManager         $userManager
     */
    public function __construct(TwigEngine $templating, ThemeManager $themeManager, AuthenticationUtils $authenticationUtils, UserManager $userManager)
    {
        $this->templating = $templating;
        $this->themeManager = $themeManager;
        $this->authenticationUtils = $authenticationUtils;
        $this->userManager = $userManager;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws CircularFallbackException
     */
    public function loginAction(Request $request): Response
    {
        $session = new Session();

        if ($returnUrl = $request->get('returnUrl')) {
            $session->set('returnUrl', $returnUrl);
        }

        if ($this->getUser()) {
            $returnUrl = $session->get('returnUrl', '/');
            return $this->redirect($returnUrl);
        }

        $context = [
            'login_enabled' => $this->userManager->isEnabled(),
            'last_username' => $this->authenticationUtils->getLastUsername(),
            'error' => $this->authenticationUtils->getLastAuthenticationError(),
        ];
        return $this->render($this->themeManager->locateTemplate('security/login.html.twig'), $context);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function verifyUsernameAction(Request $request): Response
    {
        $status = $this->userManager->getUsernameStatus($request->request->get('username'));

        return new JsonResponse(['status' => $status]);
    }

    public function logout()
    {
        //This method can be blank - it will be intercepted by the firewall
    }
}
