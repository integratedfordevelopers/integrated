<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Integrated\Bundle\PageBundle\Document\Page\ContentTypePage;
/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class ContentTypePageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ContentTypePage $contentTypePage */
        $contentTypePage = $builder->getData();

        $builder->add('path', 'text', [
            'label' => 'URL'
        ]);

        $builder->add('layout', 'integrated_page_layout_choice', [
            'theme' => $options['theme'],
            'directory' => sprintf('/content/%s', $contentTypePage->getContentType()->getId())
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'theme' => 'default',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_page_content_type_page';
    }
}
