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

use ReflectionClass;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\DomCrawler\Crawler;
use Knp\Menu\ItemInterface;


/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RoleType extends AbstractType
{

    /**
     * @var Container
     */
    private $container;

    /**
     * Constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setDefault('multiple', true);
        $resolver->setDefault('expanded', true);

        $resolver->setDefaults(array(
            'choices' => $this->getChoices(),
        ));
    }

    public function getChoices()
    {
        return $this->getRolesFromDb() + $this->getRolesFromXml() + $this->getRolesFromMenu();
    }

    public function getRolesFromDb()
    {
        $manager = $this->container->get('integrated_user.role.manager');
        $roles = $manager->findAll();

        $output = [];
        foreach ($roles as $role) {
            $output[ $role->getId() ] = $role->getRole();
        }

        return $output;
    }

    public function getRolesFromXml()
    {
        foreach ($this->container->getParameter('kernel.bundles') as $name => $class) {
            $dir = dirname((new ReflectionClass($class))->getFileName());
            $filePath = $dir.'/Resources/config/roles/roles.xml';

            if (file_exists($filePath)) {
                $content = file_get_contents($filePath);
                $crawler = new Crawler($content);
            }
        }

        return [];
    }

    public function getRolesFromMenu()
    {
        $provider = $this->container->get('integrated_menu.provider.menu_provider');

        $menu = $provider->get('integrated_menu');

        $output = [];
        /** @var ItemInterface $item */
        foreach ($menu as $item) {
            $output[$item->getName()] = $item->getName();
        }

        return $output;
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
        return 'integrated_user_role_choice';
    }
}