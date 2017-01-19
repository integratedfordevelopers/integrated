<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\CommentBundle\Controller;

use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Bundle\CommentBundle\Document\Comment;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\UserBundle\Model\User;

use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class DefaultController
{
    /**
     * @var TwigEngine
     */
    protected $templating;

    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @param TwigEngine $templating
     * @param DocumentManager $dm
     * @param FormFactory $formFactory
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        TwigEngine $templating,
        DocumentManager $dm,
        FormFactory $formFactory,
        TokenStorageInterface $tokenStorage
    ) {
        $this->templating = $templating;
        $this->dm = $dm;
        $this->formFactory = $formFactory;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param Content $content
     * @param string  $field
     * @param Request $request
     * @return array|JsonResponse
     */
    public function newAction(Request $request, Content $content, $field)
    {
        $form = $this->formFactory->create('integrated_comment', null, ['field' => $field]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();


            $comment = new Comment();
            $comment->setContent($content);
            $comment->setText($data['text']);
            $comment->setField($field);

            $user = $this->getUser();
            if ($user instanceof User && $relation = $user->getRelation()) {
                $comment->setAuthor($relation);
            }

            $this->dm->persist($comment);

            if ($data['parent']) {
                $parentComment = $this->dm->getRepository(Comment::class)->find($data['parent']);
                if ($parentComment instanceof Comment) {
                    $parentComment->addChild($comment);
                }
            }

            $this->dm->flush();

            return new JsonResponse(['id' => $comment->getId()]);
        }

        return $this->templating->renderResponse('IntegratedCommentBundle:Comment:new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Comment $comment
     * @return array
     */
    public function getAction(Comment $comment)
    {
        $form = $this->formFactory->create('integrated_comment', null, ['parent' => $comment->getId(), 'field' => $comment->getField()]);

        return $this->templating->renderResponse('IntegratedCommentBundle:Comment:get.html.twig',[
            'comment' => $comment,
            'form' => $form->createView(),
        ]);

    }

    /**
     * @return UserInterface|null
     */
    protected function getUser()
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return null;
        }

        if (!is_object($user = $token->getUser())) {
            return null;
        }

        return $user;
    }
}
