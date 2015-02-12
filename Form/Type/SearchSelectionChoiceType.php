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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;

use Integrated\Bundle\ContentBundle\Form\DataTransformer\SearchSelectionChoiceTransformer;
use Integrated\Bundle\UserBundle\Model\UserInterface;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class SearchSelectionChoiceType extends AbstractType
{
    /**
     * @var \Integrated\Bundle\ContentBundle\Document\SearchSelection\SearchSelectionRepository
     */
    private $repository;

    /**
     * @var SecurityContext
     */
    private $securityContext;

    /**
     * @param ManagerRegistry $manager
     * @param SecurityContext $securityContext
     */
    public function __construct(ManagerRegistry $manager, SecurityContext $securityContext)
    {
        $this->repository = $manager->getManager()->getRepository('IntegratedContentBundle:SearchSelection\SearchSelection');
        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new SearchSelectionChoiceTransformer($this->repository));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $choices = [];

        if ($user = $this->getUser()) {

            foreach ($this->repository->findPublicByUserId($user->getId()) as $selection) {
                /** @var \Integrated\Bundle\ContentBundle\Document\SearchSelection\SearchSelection $selection */
                $choices[$selection->getId()] = $selection->getTitle();
            }
        }

        $resolver->setDefaults([
            'choices' => $choices,
        ]);
    }

    /**
     * @return UserInterface
     */
    private function getUser()
    {
        if ($token = $this->securityContext->getToken()) {
            $user = $token->getUser();

            if ($user instanceof UserInterface) {
                return $user;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_search_selection_choice';
    }
}