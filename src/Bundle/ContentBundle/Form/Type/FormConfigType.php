<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\Type;

use Integrated\Common\ContentType\ContentTypeInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormConfigType extends AbstractType
{
    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, ['disabled' => $options['form_config_key'] === 'default']);
        $builder->add('fields', FormConfigFieldsType::class, ['content_type' => $options['form_config_content_type']]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $contentTypeNormalizer = function (Options $options, $value) {
            if (!$value instanceof ContentTypeInterface && !$value = $this->registry->getRepository(ContentTypeInterface::class)->find($value)) {
                throw new InvalidOptionsException(sprintf(
                    'The option "form_config_content_type" with value %s could not be resolved to a %s',
                    $value,
                    ContentTypeInterface::class
                ));
            }

            return $value;
        };

        $resolver->setRequired('form_config_content_type');
        $resolver->setAllowedTypes('form_config_content_type', ['string', ContentTypeInterface::class]);
        $resolver->setNormalizer('form_config_content_type', $contentTypeNormalizer);

        $resolver->setDefault('form_config_key', null);
        $resolver->setAllowedTypes('form_config_key', ['string', 'null']);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_content_form_config';
    }
}
