<?php

namespace Integrated\Bundle\CommentBundle\Controller;

use Integrated\Bundle\CommentBundle\Document\Comment;
use Integrated\Bundle\CommentBundle\Document\Embedded\Author;
use Integrated\Bundle\CommentBundle\Form\Type\CommentType;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\UserBundle\Model\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CommentController
 */
class CommentController extends Controller
{
    /**
     * @Template()
     *
     * @param Content $content
     * @param Request $request
     * @return array
     */
    public function newAction(Content $content, Request $request)
    {
        $form = $this->createForm(new CommentType());

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();

                /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
                $dm = $this->get('doctrine_mongodb')->getManager();

                /** @var User $user */
                $user = $this->get('security.token_storage')->getToken()->getUser();

                $author = new Author($user->getId(), $user->getUsername());

                $comment = new Comment();
                $comment->setAuthor($author);
                $comment->setContent($content);
                $comment->setText($data['text']);

                $dm->persist($comment);
                $dm->flush();

                if ($data['parent']) {
                    $parentComment = $this->get('doctrine.odm.mongodb.document_manager')->find('IntegratedCommentBundle:Comment', $data['parent']);
                    if ($parentComment) {
                        $parentComment->addChildren($comment);
                        $dm->flush();
                    }
                }

                return new JsonResponse(['id' => $comment->getId()]);
            }
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Template()
     *
     * @param Comment $comment
     * @return array
     */
    public function getAction(Comment $comment)
    {
        $form = $this->createForm(new CommentType(), null, ['parent' => $comment->getId()]);

        return [
            'comment' => $comment,
            'author' => $this->get('integrated_user.user.manager')->find($comment->getAuthor()->getUserId()),
            'form' => $form->createView(),
        ];

    }
}
