<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ImportBundle\Form\Type;

use Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Bundle\ContentBundle\Form\Type\ContentTypeChoice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;

class ImportDefinitionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class);
        $builder->add('contentType', ContentTypeChoice::class, ['label' => 'Content type', 'multiple' => false]);
        $builder->add('channels', DocumentType::class, [
            'label' => 'Channels',
            'class' => Channel::class,
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => true,
        ]);
        $builder->add('imageBaseUrl', UrlType::class, ['label' => 'Base URL for images', 'required' => false]);
        $builder->add(
            'imageContentType',
            ContentTypeChoice::class,
            ['label' => 'Content type for images', 'required' => false, 'multiple' => false]
        );
        $builder->add(
            'imageRelation',
            DocumentType::class,
            [
                'label' => 'Relation for images',
                'multiple' => false,
                'required' => false,
                'query_builder' => function (DocumentRepository $dr) {
                    return $dr->createQueryBuilder()->sort('name');
                },
                'class' => Relation::class,
                'choice_label' => 'name',
                'choice_value' => 'id',
            ]
        );
        $builder->add(
            'fileContentType',
            ContentTypeChoice::class,
            ['label' => 'Content type for files', 'required' => false, 'multiple' => false]
        );
        $builder->add(
            'fileRelation',
            DocumentType::class,
            [
                'label' => 'Relation for files',
                'multiple' => false,
                'required' => false,
                'query_builder' => function (DocumentRepository $dr) {
                    return $dr->createQueryBuilder()->sort('name');
                },
                'class' => Relation::class,
                'choice_label' => 'name',
                'choice_value' => 'id',
            ]
        );
    }
}
