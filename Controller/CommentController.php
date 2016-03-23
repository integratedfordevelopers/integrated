<?php

namespace Integrated\Bundle\CommentBundle\Controller;

use Integrated\Bundle\CommentBundle\Document\Comment;
use Integrated\Bundle\CommentBundle\Form\Type\CommentType;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $comment = new Comment();
        $comment->setAuthor($user);
        $comment->setContent($content);

        $form = $this->createForm(new CommentType(), $comment);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
                $dm = $this->get('doctrine_mongodb')->getManager();

                $dm->persist($comment);
                $dm->flush();

                return $this->redirect($this->generateUrl('integrated_comment_get', ['comment' => $comment->getId()]));
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
     * @param Request $request
     * @return array
     */
    public function getAction(Comment $comment, Request $request)
    {
        $form = $this->createForm(new CommentType(), $comment);

        return [
            'comment' => $comment,
            'author' => $this->get('integrated_user.user.manager')->find($comment->getAuthor()),
            'form' => $form->createView(),
        ];

    }
}
