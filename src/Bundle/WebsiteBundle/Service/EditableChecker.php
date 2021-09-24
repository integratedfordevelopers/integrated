<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WebsiteBundle\Service;

use Integrated\Bundle\WebsiteBundle\Routing\ContentTypePageLoader;
use Integrated\Bundle\WebsiteBundle\Routing\PageLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class EditableChecker
{
    /**
     * @var AuthorizationChecker
     */
    protected $authorizationChecker;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var Request|null
     */
    protected $request;

    /**
     * @param AuthorizationChecker  $authorizationChecker
     * @param TokenStorageInterface $tokenStorage
     * @param RequestStack          $requestStack
     */
    public function __construct(
        AuthorizationChecker $authorizationChecker,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->request = $requestStack->getMasterRequest();
    }

    /**
     * @return bool
     */
    public function checkEditable()
    {
        if (null === $this->request) {
            return false;
        }

        if (null === $this->tokenStorage->getToken()) {
            return false;
        }

        if (!$this->authorizationChecker->isGranted('ROLE_WEBSITE_MANAGER') && !$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return false;
        }

        $route = $this->request->attributes->get('_route');

        //check if route begins with page or contentTypePage prefix
        if (0 === strpos($route, ContentTypePageLoader::ROUTE_PREFIX) ||
            0 === strpos($route, PageLoader::ROUTE_PREFIX)
        ) {
            return true;
        }

        return false;
    }
}
