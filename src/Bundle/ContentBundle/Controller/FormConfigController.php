<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Controller;

use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Bundle\ContentBundle\Form\Type\ActionsType;
use Integrated\Bundle\ContentBundle\Form\Type\FormConfigCustomFieldType;
use Integrated\Bundle\ContentBundle\Form\Type\FormConfigType;
use Integrated\Bundle\ContentBundle\FormConfig\Handler;
use Integrated\Common\FormConfig\Exception\NotFoundException;
use Integrated\Common\FormConfig\FormConfigManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;

class FormConfigController extends Controller
{
    /**
     * @var FormConfigManagerInterface
     */
    private $manager;

    /**
     * @var Handler
     */
    private $handler;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @param FormConfigManagerInterface $manager
     * @param Serializer                 $serializer
     * @param ContainerInterface         $container
     */
    public function __construct(
        FormConfigManagerInterface $manager,
        Handler $handler,
        Serializer $serializer,
        ContainerInterface $container
    ) {
        $this->manager = $manager;
        $this->handler = $handler;
        $this->serializer = $serializer;
        $this->container = $container;
    }

    /**
     * @param Request     $request
     * @param ContentType $type
     *
     * @return Response
     */
    public function indexAction(Request $request, ContentType $type)
    {
        return $this->render('IntegratedContentBundle:form_config:index.'.$request->getRequestFormat().'.twig', [
            'configs' => $this->manager->all($type),
        ]);
    }

    /**
     * @param Request     $request
     * @param ContentType $type
     * @param string      $key
     *
     * @return Response
     */
    public function editAction(Request $request, ContentType $type, string $key = null)
    {
        $config = [];

        if ($key) {
            try {
                $config = $this->manager->get($type, $key);
            } catch (NotFoundException $e) {
                throw $this->createNotFoundException('Not Found', $e);
            }

            $config = [
                'name' => $config->getName(),
                'fields' => $config->getFields(),
            ];
        }

        $form = $this->createForm(FormConfigType::class, $config, ['form_config_content_type' => $type, 'form_config_key' => $key]);
        $form->add('actions', ActionsType::class, ['buttons' => ['save', 'cancel']]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('actions')->getData() === 'save') {
                $this->handler->handle($type, $key, $form->getData());
            }

            return $this->redirect($this->generateUrl('integrated_content_content_type_show', [
                'id' => $type->getId(),
            ]));
        }

        return $this->render('IntegratedContentBundle:form_config:'.($config ? 'edit' : 'new').'.html.twig', [
            'type' => $type,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param ContentType $type
     * @param string      $key
     *
     * @return Response
     */
    public function deleteAction(ContentType $type, string $key)
    {
        if ($this->manager->has($type, $key)) {
            $this->manager->remove($this->manager->get($type, $key));

            if ($this->container->has('braincrafted_bootstrap.flash')) {
                $this->container->get('braincrafted_bootstrap.flash')->success('Form configuration deleted');
            }
        }

        return $this->redirect($this->generateUrl('integrated_content_content_type_show', [
            'id' => $type->getId(),
        ]));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function customAction(Request $request, ContentType $type)
    {
        $form = $this->createForm(FormConfigCustomFieldType::class, null, [
            'action' => $this->generateUrl('integrated_content_form_config_custom', ['type' => $type->getId()]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return JsonResponse::fromJsonString($this->serializer->serialize($form->getData(), 'json'));
        }

        return $this->render('IntegratedContentBundle:form_config:custom.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
