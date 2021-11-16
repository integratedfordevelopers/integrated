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

use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType;
use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class PrimaryChannelType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * PrimaryChannelType constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Channel::class,
            'choice_label' => 'name',
            'required' => false,
            'attr' => [
                'class' => 'primary-channel',
                'data-make-primary-text' => $this->translator->trans('make primary'),
            ],
        ]);
    }

    public function getParent()
    {
        return DocumentType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_primary_channel';
    }
}
