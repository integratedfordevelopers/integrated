<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\Type\Job;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Braincrafted\Bundle\BootstrapBundle\Form\Type\BootstrapCollectionType;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class ContactPersonsType extends BootstrapCollectionType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'type' => 'integrated_company_job',
            'allow_add' => true,
            'allow_delete' => true,
            'label' => 'Contact persons',
            'delete_button_text' => 'x'
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'integrated_contact_persons';
    }
}
