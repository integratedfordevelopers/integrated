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

use Integrated\Bundle\UserBundle\Form\EventListener\SecurityLoginListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class LoginFormType extends AbstractType
{
    /**
     * @var RequestStack
     */
    private $request;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $translationDomain;

    /**
     * Create a login form type used for authentication.
     *
     * The container is used to retrieve the request so that the errors
     * and last username can be extracted from it.
     *
     * @param RequestStack        $request
     * @param TranslatorInterface $translator
     * @param null                $translationDomain
     */
    public function __construct(RequestStack $request, TranslatorInterface $translator = null, $translationDomain = null)
    {
        $this->request = $request;

        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('_username', Type\TextType::class);
        $builder->add('_password', Type\PasswordType::class);

        if ($options['auth_remember']) {
            $builder->add(
                '_remember_me',
                Type\CheckboxType::class,
                [
                    'required' => false,
                    'attr' => [
                        'align_with_widget' => true,
                    ],
                ]
            );
        }

        if ($options['auth_target_path']) {
            $config = [];

            if ($options['auth_target_path'] === (string) $options['auth_target_path']) {
                $config['data'] = $options['auth_target_path'];
                $config['mapped'] = false;
            }

            $builder->add('_target_path', HiddenType::class, $config);
        }

        $builder->add('login', Type\SubmitType::class);

        if ($request = $this->getRequest($options)) {
            $builder->addEventSubscriber(new SecurityLoginListener($request, $this->getTranslator($options), $this->getTranslationDomain($options)));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['full_name'] = ''; // field names should not be prefixed
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('method', 'post');

        // form_login csrf token name is by default "_csrf_token"
        // and the intention is by default "authenticate" so set
        // those values as default for this form.

        $resolver->setDefault('csrf_field_name', '_csrf_token');
        $resolver->setDefault('csrf_token_id', 'authenticate');

        $resolver->setDefault('auth_remember', true);
        $resolver->setDefault('auth_target_path', null);

        $resolver->setDefined(['request', 'translator', 'translation_domain']);

        $resolver->setAllowedTypes('request', ['null', 'Symfony\\Component\\HttpFoundation\\Request']);
        $resolver->setAllowedTypes('translator', ['null', 'Symfony\\Component\\Translation\\TranslatorInterface']);
        $resolver->setAllowedTypes('translation_domain', ['null', 'string']);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_user_security_login_form';
    }

    /**
     * Get the request object.
     *
     * This will first look if the there is a request object in the
     * options and if not uses the one from the request stack. If null
     * is supplied as request object in the options then the request
     * object will be disabled
     *
     * @param array $options
     */
    protected function getRequest(array $options = [])
    {
        if (\array_key_exists('request', $options)) {
            return $options['request'];
        }

        return $this->request->getCurrentRequest();
    }

    /**
     * Get the translator object.
     *
     * This will first look if there is a translator object in the
     * options and if not uses the injected one. if none is present
     * then a dummy will be returned.
     *
     * @param array $options
     *
     * @return TranslatorInterface
     */
    protected function getTranslator(array $options = [])
    {
        if (\array_key_exists('translator', $options)) {
            return $options['translator'] ? $options['translator'] : new IdentityTranslator(); // in case of null return a dummy
        }

        if ($this->translator === null) {
            $this->translator = new IdentityTranslator();
        }

        return $this->translator;
    }

    /**
     * Get the translation domain.
     *
     * This will first look if there is a translation domain in the
     * options and if not uses the injected on.
     *
     * @param array $options
     */
    protected function getTranslationDomain(array $options = [])
    {
        if (\array_key_exists('translation_domain', $options)) {
            return $options['translation_domain'];
        }

        return $this->translationDomain;
    }
}
