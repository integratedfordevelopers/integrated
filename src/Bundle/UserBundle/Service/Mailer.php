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
     * @var string|null
     */
    private $from;

    /**
     * @var string|null
     */
    private $name;

    /**
     * Password constructor.
     *
     * @param UserManager         $userManager
     * @param \Swift_Mailer       $mailer
     * @param TwigEngine          $templating
     * @param TranslatorInterface $translator
     * @param KeyGenerator        $keyGenerator
     * @param                     $from
     * @param                     $name
     */
    public function __construct(UserManager $userManager, \Swift_Mailer $mailer, TwigEngine $templating, TranslatorInterface $translator, KeyGenerator $keyGenerator, $from, $name)
    {
        $this->userManager = $userManager;
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->translator = $translator;
        $this->keyGenerator = $keyGenerator;
        $this->from = $from;
        $this->name = $name;
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
            ->setFrom($this->from, $this->name)
            ->setTo($email)
            ->setBody(
                $this->templating->render($template, $data),
                'text/html'
            );
        $this->mailer->send($message);

        return true;
    }
}
