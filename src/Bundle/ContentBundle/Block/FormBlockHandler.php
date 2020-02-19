<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Block;

use Braincrafted\Bundle\BootstrapBundle\Form\Type\FormActionsType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\BlockBundle\Block\BlockHandler;
use Integrated\Bundle\ContentBundle\Document\Block\FormBlock;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation;
use Integrated\Bundle\ContentBundle\Event\FormBlockEvent;
use Integrated\Bundle\ContentBundle\Mailer\FormMailer;
use Integrated\Common\Block\BlockInterface;
use Integrated\Common\Content\Channel\ChannelContextInterface;
use Integrated\Common\Content\Form\ContentFormType;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Vihuvac\Bundle\RecaptchaBundle\Form\Type\VihuvacRecaptchaType;
use Vihuvac\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue;

/**
 * Form block handler.
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class FormBlockHandler extends BlockHandler
{
    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var DocumentManager
     */
    protected $documentManager;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var FormMailer
     */
    protected $formMailer;

    /**
     * @var ChannelContextInterface
     */
    protected $channelContext;

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @param FormFactory             $formFactory
     * @param DocumentManager         $documentManager
     * @param RequestStack            $requestStack
     * @param FormMailer              $formMailer
     * @param ChannelContextInterface $channelContext
     * @param EventDispatcher         $eventDispatcher
     */
    public function __construct(
        FormFactory $formFactory,
        DocumentManager $documentManager,
        RequestStack $requestStack,
        FormMailer $formMailer,
        ChannelContextInterface $channelContext,
        EventDispatcher $eventDispatcher
    ) {
        $this->formFactory = $formFactory;
        $this->documentManager = $documentManager;
        $this->requestStack = $requestStack;
        $this->formMailer = $formMailer;
        $this->channelContext = $channelContext;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockInterface $block, array $options)
    {
        if (!$block instanceof FormBlock) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();

        if (!$request instanceof Request) {
            return;
        }

        $contentType = $block->getContentType();

        $content = $contentType->create();

        $this->eventDispatcher->dispatch(FormBlockEvent::PRE_LOAD, new FormBlockEvent($content, $block));

        $form = $this->createForm($content, ['method' => 'post', 'content_type' => $contentType], $block);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->linkActiveDocument($block, $content);

                if ($channel = $this->channelContext->getChannel()) {
                    $content->addChannel($channel);
                }

                $this->eventDispatcher->dispatch(FormBlockEvent::PRE_FLUSH, new FormBlockEvent($content, $block));

                $this->documentManager->persist($content);
                $this->documentManager->flush();

                $this->eventDispatcher->dispatch(FormBlockEvent::POST_FLUSH, new FormBlockEvent($content, $block));

                $data = $request->request->get($form->getName());

                // remove irrelevant fields
                unset($data['actions']);
                unset($data['_token']);

                $this->formMailer->send($data, $block->getEmailAddresses(), $block->getTitle());

                if ($block->getReturnUrl()) {
                    return new RedirectResponse($block->getReturnUrl());
                }
            }
        }

        return $this->render([
            'block' => $block,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param mixed     $data
     * @param array     $options
     * @param FormBlock $block
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createForm($data = null, array $options = [], FormBlock $block = null)
    {
        $form = $this->formFactory->createBuilder(ContentFormType::class, $data, $options);

        // remove irrelevant fields
        $form->remove('slug');
        $form->remove('disabled');
        $form->remove('publishTime');
        $form->remove('authors');
        $form->remove('channels');
        $form->remove('relations');
        $form->remove('extension_workflow');
        $form->remove('source');
        $form->remove('sourceUrl');

        if ($form->has('address')) {
            $form->get('address')->remove('type');
        }

        if ($form->has('content')) {
            $form->remove('content');
            $form->remove('description');

            $form->add('content', TextareaType::class, [
                'mapped' => true,
                'label' => 'Description',
            ]);
        }

        if (null !== $block && $block->isRecaptcha()) {
            $form->add('recaptcha', VihuvacRecaptchaType::class, [
                'mapped' => false,
                'label' => ' ',
                'constraints' => [
                    new IsTrue(),
                ],
            ]);
        }

        $form->add('actions', FormActionsType::class, [
            'buttons' => [
                'submit' => ['type' => SubmitType::class],
            ],
        ]);

        return $form->getForm();
    }

    /**
     * Link active document to content item as integrated relation.
     *
     * @param FormBlock $block
     * @param Content   $content
     */
    public function linkActiveDocument(FormBlock $block, Content $content)
    {
        if (!$this->getDocument() || !$block->getLinkRelation()) {
            return;
        }

        $relation = new Relation();
        $relation->setRelationId($block->getLinkRelation()->getId());
        $relation->setRelationType($block->getLinkRelation()->getType());
        $relation->setReferences(new ArrayCollection([$this->getDocument()]));

        $content->addRelation($relation);
    }
}
