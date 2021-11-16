<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Form\Type;

use Symfony\Contracts\Translation\TranslatorInterface;
use Integrated\Bundle\UserBundle\Form\EventListener\AuthenticatorCheckerListener;
use Integrated\Bundle\UserBundle\Model\UserInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AuthenticatorType extends AbstractType
{
    /**
     * @var TokenStorageInterface
     */
    private $storage;

    /**
     * @var GoogleAuthenticatorInterface
     */
    private $authenticator;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $translationDomain;

    public function __construct(TokenStorageInterface $storage, GoogleAuthenticatorInterface $authenticator, TranslatorInterface $translator, string $translationDomain = null)
    {
        $this->storage = $storage;
        $this->authenticator = $authenticator;
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new AuthenticatorCheckerListener(
            $options['user'],
            $this->authenticator,
            $this->translator,
            $this->translationDomain
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'mapped' => false,
            'attr' => [
                'autocomplete' => 'one-time-code',
                'inputmode' => 'numeric',
                'pattern' => '[0-9]*',
            ],
            'user' => null,
        ]);

        $resolver->setAllowedTypes('user', [UserInterface::class, 'null']);
        $resolver->setNormalizer('user', function (Options $options, $value) {
            if ($value) {
                return $value;
            }

            $token = $this->storage->getToken();

            if ($token) {
                $value = $token->getUser();

                if ($value instanceof UserInterface) {
                    return $value;
                }
            }

            throw new InvalidOptionsException(sprintf('The option "user" with is expected to be of type "%s"', UserInterface::class));
        });
    }

    public function getParent()
    {
        return TextType::class;
    }
}
