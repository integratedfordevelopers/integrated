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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class SearchSelectionType extends AbstractType
{
    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text');

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {

            $builder->add('public', 'choice', [
                'label' => 'Available for',
                'expanded' => true,
                'choices' => [
                    1 => 'Everyone',
                    0 => 'Me only',
                ],
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_search_selection';
    }
}
