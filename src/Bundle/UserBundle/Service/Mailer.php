<?php
/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Service;

use Integrated\Bundle\UserBundle\Doctrine\UserManager;
use Integrated\Bundle\UserBundle\Model\ScopeInterface;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Translation\TranslatorInterface;

class Mailer
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var TwigEngine
     */
    private $templating;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var KeyGenerator
     */
    private $keyGenerator;

    /**
     * Password constructor.
     *
     * @param UserManager             $userManager
     * @param \Swift_Mailer           $mailer
     * @param TwigEngine              $templating
     * @param RouterInterface         $router
     * @param TranslatorInterface     $translator
     * @param EncoderFactoryInterface $encoderFactory
     * @param KeyGenerator            $keyGenerator
     */
    public function __construct(UserManager $userManager, \Swift_Mailer $mailer, TwigEngine $templating, RouterInterface $router, TranslatorInterface $translator, KeyGenerator $keyGenerator)
    {
        $this->userManager = $userManager;
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->translator = $translator;
        $this->keyGenerator = $keyGenerator;
    }

    /**
     * @param string              $email
     * @param ScopeInterface|null $scope
     *
     * @return bool
     *
     * @throws \Twig\Error\Error
     */
    public function sendPasswordResetMail(string $email, ScopeInterface $scope = null): bool
    {
        $data = [
            'subject' => '[Integrated] '.$this->translator->trans('Password reset'),
        ];
        $template = 'IntegratedUserBundle::mail/password.reset.notfound.html.twig';

        if ($user = $this->userManager->findByUsernameAndScope($email, $scope)) {
            if ($user->isEnabled()) {
                $timestamp = time();
                $key = $this->keyGenerator->generateKey($timestamp, $user);
                $template = 'IntegratedUserBundle::mail/password.reset.html.twig';

                $data['user'] = $user;
                $data['timestamp'] = $timestamp;
                $data['key'] = $key;
            }
        }

        $message = (new \Swift_Message())
            ->setSubject($data['subject'])
            ->setFrom('mailer@integratedforpublishers.com')
            ->setTo($email)
            ->setBody(
                $this->templating->render($template, $data),
                'text/html'
            );
        $this->mailer->send($message);

        return true;
    }
}
