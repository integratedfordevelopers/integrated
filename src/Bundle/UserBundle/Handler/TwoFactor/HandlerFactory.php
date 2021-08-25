<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Handler\TwoFactor;

use Integrated\Bundle\UserBundle\Security\TwoFactor\Http\Context;
use Symfony\Component\Form\FormFactory;
use Twig\Environment;

class HandlerFactory implements HandlerFactoryInterface
{
    /**
     * @var string[]
     */
    private $templates;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var FormFactory
     */
    private $factory;

    public function __construct(array $templates, Environment $twig, FormFactory $factory)
    {
        $this->templates = $templates;
        $this->twig = $twig;
        $this->factory = $factory;
    }

    public function create(Context $context): HandlerInterface
    {
        $template = $this->templates[$context->getFirewall()] ?? null;

        if (!$template) {
            throw new \InvalidArgumentException(sprintf('No template found for firewall %s', $context->getFirewall()));
        }

        return new Handler($context, $template, $this->twig, $this->factory);
    }
}
