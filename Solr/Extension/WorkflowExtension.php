<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Solr\Extension;

use Doctrine\Common\Persistence\ObjectManager;
use Integrated\Bundle\WorkflowBundle\Entity\Workflow\State;
use Integrated\Common\Content\ContentInterface;

use Integrated\Common\ContentType\Resolver\ContentTypeResolverInterface;
use Integrated\Common\Converter\ContainerInterface;
use Integrated\Common\Converter\Type\TypeExtensionInterface;
use Symfony\Component\Security\Core\Util\ClassUtils;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class WorkflowExtension implements TypeExtensionInterface
{
    /**
   	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
   	 */
   	private $container;

   	/**
   	 * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   	 */
   	public function __construct(\Symfony\Component\DependencyInjection\ContainerInterface $container)
   	{
   		$this->container = $container;
   	}

    /**
     * {@inheritdoc}
     */
    public function build(ContainerInterface $container, $data, array $options = [])
    {
        if (!$data instanceof ContentInterface) {
            return; // only process content
        }

        if (!$state = $this->resolveState($data)) {
            return; // got not workflow
        }

        $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated.content';
    }

    /**
   	 * @return ObjectManager
   	 */
   	protected function getManager()
   	{
   		return $this->container->get('integrated_workflow.extension.doctrine.object_manager');
   	}

   	/**
   	 * @return ContentTypeResolverInterface
   	 */
   	protected function getResolver()
   	{
        return $this->container->get('integrated.form.resolver');
   	}

    /**
   	 * @param ContentInterface $content
     *
   	 * @return null | State
   	 */
   	protected function resolveState(ContentInterface $content)
   	{
		// does this content even have a workflow connected ?

		if (!$type = $this->getResolver()->getType(ClassUtils::getRealClass($content), $content->getContentType())) {
            return null;
		}

        if (!$type->getOption('workflow')) {
            return null;
        }

   		$repository = $this->getManager()->getRepository('Integrated\\Bundle\\WorkflowBundle\\Entity\\Workflow\\State');

   		if ($entity = $repository->findOneBy(['content' => $content])) {
   			return $entity;
   		}

        // get default workflow

        $repository = $this->getManager()->getRepository('Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition');

        if ($entity = $repository->find($type->getOption('workflow'))) {
            return $entity->getDefault();
        }

   		return null;
   	}
}
