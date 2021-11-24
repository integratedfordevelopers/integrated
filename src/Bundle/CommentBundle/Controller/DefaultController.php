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
use Integrated\Bundle\CommentBundle\Document\Embedded\Reply;
use Integrated\Bundle\CommentBundle\Form\Type\CommentType;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\UserBundle\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class DefaultController.
 */
class DefaultController extends AbstractController
{
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
     * @param DocumentManager       $dm
     * @param FormFactory           $formFactory
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        DocumentManager $dm,
        FormFactory $formFactory,
        TokenStorageInterface $tokenStorage
    ) {
        $this->dm = $dm;
        $this->formFactory = $formFactory;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param Content $content
     * @param string  $field
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response|JsonResponse
     */
    public function new(Request $request, Content $content, $field)
    {
        $comment = new Comment();
        $comment->setContent($content);
        $comment->setField($field);

        $user = $this->getUser();
        if ($user instanceof User && $relation = $user->getRelation()) {
            $comment->setAuthor($relation);
        }

        $form = $this->formFactory->create(CommentType::class, $comment, [
            'action' => $request->getUri(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->dm->persist($comment);
            $this->dm->flush();

            return new JsonResponse(['id' => $comment->getId()]);
        }

        return $this->render('@IntegratedComment/comment/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Comment $comment
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function getComment(Request $request, Comment $comment)
    {
        $reply = new Reply();
        $reply->setDate(new \DateTime());

        $user = $this->getUser();
        if ($user instanceof User && $relation = $user->getRelation()) {
            $comment->setAuthor($relation);
        }

        $form = $this->formFactory->create(CommentType::class, $reply, [
            'action' => $request->getUri(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->addReply($reply);
            $this->dm->flush();

            return new JsonResponse(['id' => $comment->getId()]);
        }

        return $this->render('@IntegratedComment/comment/get.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Comment $comment
     *
     * @return JsonResponse
     */
    public function delete(Comment $comment)
    {
        $this->dm->remove($comment);
        $this->dm->flush();

        return new JsonResponse([
            'deleted' => true,
            'id' => $comment->getId(),
        ]);
    }

    /**
     * @param Comment $comment
     * @param $replyId
     *
     * @return JsonResponse
     */
    public function deleteReply(Comment $comment, $replyId)
    {
        $result = $comment->removeReplyById($replyId);

        $this->dm->flush();

        return new JsonResponse([
            'deleted' => $result,
            'id' => $replyId,
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

        if (!\is_object($user = $token->getUser())) {
            return null;
        }

        return $user;
    }
}
