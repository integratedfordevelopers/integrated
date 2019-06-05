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

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\PageBundle\Document\Page\Page;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageCopyPagesType extends AbstractType
{
    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * PageCopyType constructor.
     *
     * @param DocumentManager $documentManager
     */
    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $queryBuilder = $this->documentManager->createQueryBuilder(Page::class)
            ->field('channel.$id')->equals($options['channel']);

        $result = $queryBuilder->getQuery()->execute();
        foreach ($result as $page) {
            $builder->add('page'.$page->getId(), PageCopyPageType::class, [
                'page' => $page,
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['channel', 'targetChannel']);
        $resolver->setAllowedTypes('channel', 'string');
        $resolver->setAllowedTypes('targetChannel', 'string');
    }
}
