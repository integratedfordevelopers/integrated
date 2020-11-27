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

class Password
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
     * Password constructor.
     *
     * @param UserManager   $userManager
     * @param \Swift_Mailer $mailer
     * @param TwigEngine    $templating
     */
    public function __construct(UserManager $userManager, \Swift_Mailer $mailer, TwigEngine $templating)
    {
        $this->userManager = $userManager;
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    /**
     * @param string              $email
     *
     * @param ScopeInterface|null $scope
     *
     * @return bool
     * @throws \Twig\Error\Error
     */
    public function sendResetMail(string $email, ScopeInterface $scope = null)
    {
        if (!$user = $this->userManager->findByUsernameAndScope($email, $scope)) {
            return false;
        }

        if (!$user->isEnabled()) {
            return false;
        }

        $timestamp = time();
        $hash = md5($timestamp.$user->getPassword().$user->getId());

        $message = (new \Swift_Message())
            ->setSubject('[Integrated] Password reset')
            ->setFrom('mailer@integratedforpublishers.com')
            ->setTo($user->getUsername())
            ->setBody(
                $this->templating->render(
                    'IntegratedContentBundle::mail.html.twig',
                    [
                        'subject' => '[Integrated] Password reset',
                        'body' => 'Click the button below to reset your Integrated password.',
                        'buttonLink' => 'http://localhost/'.$hash,
                        'buttonText' => 'Reset password',
                    ]
                ),
                'text/html'
            );
        $this->mailer->send($message);

        return true;
    }
}
