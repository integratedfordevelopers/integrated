<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionController extends AbstractController
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * SessionController constructor.
     *
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @param string  $sessionId
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function enterSession(string $sessionId, Request $request)
    {
        $page = $request->get('path', '/');

        $response = new RedirectResponse($page);

        if ($this->getUser() == false) {
            $sessionId = preg_replace('/[^a-zA-Z0-9]+/', '', $sessionId);

            $response->headers->setCookie(Cookie::create('PHPSESSID', $sessionId));
        }

        return $response;
    }
}
