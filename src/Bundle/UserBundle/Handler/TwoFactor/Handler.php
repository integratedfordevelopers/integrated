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

use Integrated\Bundle\UserBundle\Form\Type\AuthenticatorFormType;
use Integrated\Bundle\UserBundle\Security\TwoFactor\Http\Context;
use Symfony\Component\Form\FormFactory;
use Twig\Environment;

class Handler implements HandlerInterface
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var string
     */
    private $template;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var FormFactory
     */
    private $factory;

    public function __construct(Context $context, string $template, Environment $twig, FormFactory $factory)
    {
        $this->context = $context;
        $this->template = $template;
        $this->twig = $twig;
        $this->factory = $factory;
    }

    public function render(): string
    {
        $form = $this->factory->create(AuthenticatorFormType::class, null, ['context' => $this->context]);

        $form->handleRequest($this->context->getRequest());

        return $this->twig->render($this->template, [
            'context' => $this->context,
            'user' => $this->context->getUser(),
            'config' => $this->context->getConfig(),
            'form' => $form->createView(),
        ]);
    }

    public function validate(): bool
    {
        $form = $this->factory->create(AuthenticatorFormType::class, null, ['context' => $this->context]);
        $form->handleRequest($this->context->getRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            return true;
        }

        return false;
    }
}
