<?php

namespace Integrated\Bundle\WebsiteBundle\Controller;

use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Integrated\Bundle\ThemeBundle\Exception\CircularFallbackException;
use Integrated\Bundle\ThemeBundle\Templating\ThemeManager;
use Integrated\Bundle\WebsiteBundle\Security\IntegratedAuthenticator;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\TwigBundle\TwigEngine;
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
     * @var IntegratedAuthenticator
     */
    private $authenticator;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @param TwigEngine              $templating
     * @param ThemeManager            $themeManager
     * @param AuthenticationUtils     $authenticationUtils
     * @param ChannelContextInterface $channelContext
     */
    public function __construct(TwigEngine $templating, ThemeManager $themeManager, AuthenticationUtils $authenticationUtils, ChannelContextInterface $channelContext)
    {
        $this->templating = $templating;
        $this->themeManager = $themeManager;
        $this->authenticationUtils = $authenticationUtils;
        $this->channelContext = $channelContext;
    }

    /**
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

        $loginEnabled = false;
        if ($channel = $this->channelContext->getChannel()) {
            if ($channel instanceof Channel && $channel->getScope() !== null) {
                $loginEnabled = true;
            }
        }

        // get the login error if there is one
        $error = $this->authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $this->authenticationUtils->getLastUsername();

        $context = [
            'login_enabled' => $loginEnabled,
            'last_username' => $lastUsername,
            'error' => $error,
        ];
        return $this->render($this->themeManager->locateTemplate('security/login.html.twig'), $context);
    }

    public function logout()
    {
        //This method can be blank - it will be intercepted by the firewall
    }
}
