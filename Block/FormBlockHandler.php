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

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Bundle\BlockBundle\Block\BlockHandler;
use Integrated\Bundle\ContentBundle\Document\Block\FormBlock;
use Integrated\Bundle\ContentBundle\Mailer\FormMailer;
use Integrated\Common\Block\BlockInterface;
use Integrated\Common\Content\Form\FormFactory as ContentFormFactory;

use Vihuvac\Bundle\RecaptchaBundle\Form\Type\VihuvacRecaptchaType;
use Vihuvac\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue;

/**
 * Form block handler
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class FormBlockHandler extends BlockHandler
{
    /**
     * @var ContentFormFactory
     */
    protected $contentFormFactory;

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
     * @param ContentFormFactory $contentFormFactory
     * @param FormFactory $formFactory
     * @param DocumentManager $documentManager
     * @param RequestStack $requestStack
     * @param FormMailer $formMailer
     */
    public function __construct(ContentFormFactory $contentFormFactory, FormFactory $formFactory, DocumentManager $documentManager, RequestStack $requestStack, FormMailer $formMailer)
    {
        $this->contentFormFactory = $contentFormFactory;
        $this->formFactory = $formFactory;
        $this->documentManager = $documentManager;
        $this->requestStack = $requestStack;
        $this->formMailer = $formMailer;
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
        $type = $this->contentFormFactory->getType($contentType->getId());

        $content = $type->getType()->create();
        $form = $this->createForm($type, $content, ['method' => 'post'], $block);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->documentManager->persist($content);
                $this->documentManager->flush();
                
                $data = $request->request->get($form->getName());

                // remove irrelevant fields
                unset($data['actions']);
                unset($data['_token']);

                $this->formMailer->send($data, $block->getEmailAddresses());

                return new RedirectResponse($block->getReturnUrl());
            }
        }

        return $this->render([
            'block' => $block,
            'form'  => $form->createView(),
        ]);
    }

    /**
     * @param \Integrated\Common\Content\Form\FormTypeInterface $type
     * @param mixed $data
     * @param array $options
     * @param FormBlock $block
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createForm($type, $data = null, array $options = [], FormBlock $block = null)
    {
        $form = $this->formFactory->createBuilder($type, $data, $options);

        // remove irrelevant fields
        $form->remove('slug');
        $form->remove('disabled');
        $form->remove('publishTime');
        $form->remove('authors');
        $form->remove('channels');
        $form->remove('relations');
        $form->remove('extension_workflow');
        $form->remove('source');

        if (null !== $block && $block->isRecaptcha()) {
            $form->add('recaptcha', VihuvacRecaptchaType::class, [
                'mapped'      => false,
                'label'       => ' ',
                'constraints' => [
                    new IsTrue(),
                ],
            ]);
        }

        $form->add('actions', FormActionsType::class, [
            'buttons' => [
                'submit' => ['type' => SubmitType::class],
            ]
        ]);

        return $form->getForm();
    }
}
