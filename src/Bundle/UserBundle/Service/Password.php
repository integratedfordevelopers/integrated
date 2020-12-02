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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Translation\TranslatorInterface;

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
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * Password constructor.
     *
     * @param UserManager             $userManager
     * @param \Swift_Mailer           $mailer
     * @param TwigEngine              $templating
     * @param RouterInterface         $router
     * @param TranslatorInterface     $translator
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(UserManager $userManager, \Swift_Mailer $mailer, TwigEngine $templating, RouterInterface $router, TranslatorInterface $translator, EncoderFactoryInterface $encoderFactory)
    {
        $this->userManager = $userManager;
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->router = $router;
        $this->translator = $translator;
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @param string              $email
     * @param ScopeInterface|null $scope
     *
     * @return bool
     *
     * @throws \Twig\Error\Error
     */
    public function sendResetMail(string $email, ScopeInterface $scope = null)
    {
        $data = [
            'subject' => '[Integrated] '.$this->translator->trans('Password reset'),
            'body' => $this->translator->trans('A user account could with this e-mail address could not be found.'),
        ];

        if ($user = $this->userManager->findByUsernameAndScope($email, $scope)) {
            if ($user->isEnabled()) {
                $timestamp = time();
                $hash = sha1($timestamp.$user->getPassword().$user->getId());

                $data['body'] = $this->translator->trans('Click the button below to reset your Integrated password.');
                $data['buttonLink'] = $this->router->generate('integrated_user_password_change', ['id' => $user->getId(), 'timestamp' => $timestamp, 'key' => $hash], UrlGeneratorInterface::ABSOLUTE_URL);
                $data['buttonText'] = $this->translator->trans('Reset password');
            }
        }

        $message = (new \Swift_Message())
            ->setSubject($data['subject'])
            ->setFrom('mailer@integratedforpublishers.com')
            ->setTo($email)
            ->setBody(
                $this->templating->render(
                    'IntegratedContentBundle::mail.html.twig',
                    $data
                ),
                'text/html'
            );
        $this->mailer->send($message);

        return true;
    }

    /**
     * @param int    $id
     * @param int    $timestamp
     * @param string $key
     *
     * @return bool
     */
    public function isValidKey(int $id, int $timestamp, string $key)
    {
        if ($timestamp > time() || $timestamp < (time() - 24 * 3600)) {
            return false;
        }

        if (!$user = $this->userManager->find($id)) {
            return false;
        }

        return $key === sha1($timestamp.$user->getPassword().$user->getId());
    }

    /**
     * @param int    $id
     * @param int    $timestamp
     * @param string $key
     *
     * @return bool
     */
    public function changePassword(int $id, string $password)
    {
        if (!$user = $this->userManager->find($id)) {
            return false;
        }

        $salt = base64_encode(random_bytes(72));

        $user->setPassword($this->encoderFactory->getEncoder($user)->encodePassword($password, $salt));
        $user->setSalt($salt);

        $this->userManager->persist($user);

        return true;
    }
}
