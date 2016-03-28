<?php

namespace Integrated\Bundle\CommentBundle\EventListener;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\CommentBundle\Document\CommentContent;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Field;
use Integrated\Common\Content\Form\Event\BuilderEvent;
use Integrated\Common\Content\Form\Event\FieldEvent;
use Integrated\Common\Content\Form\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class ContentCommentSubscriber
 */
class CommentFormFieldsSubscriber implements EventSubscriberInterface
{
    /**
     * @var DocumentManager
     */
    protected $documentManager;

    /**
     * CommentFormFieldsSubscriber constructor.
     * @param DocumentManager $documentManager
     */
    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;

    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::BUILD_FIELD => 'onBuildField',
            Events::POST_BUILD => 'onPostBuild',
        ];
    }

    /**
     * @param FieldEvent $event
     */
    public function onBuildField(FieldEvent $event)
    {
        /** @var Field $field */
        $field = $event->getField();

        $formOptions = $event->getOptions();
        /** @var Content $content */
        $content = $formOptions['data'];

        if ($field->getType() == 'integrated_tinymce') {
            $repository = $this->documentManager->getRepository('IntegratedCommentBundle:CommentContent');
            if ($replaceContent = $repository->findOneBy(['content.$id' => $content->getId()])) {
                $setter = Inflector::camelize('set_'.$field->getName());
                $content->$setter($replaceContent->getBody());
            }
        } else {
            $options = $field->getOptions();

            $repository = $this->documentManager->getRepository('IntegratedCommentBundle:Comment');
            if ($comment = $repository->findOneBy(['content.$id' => $content->getId(), 'field' => $field->getName()])) {
                $options['attr'] = array('data-comment-id' => $comment->getId());
                $field->setOptions($options);
            }
        }
    }

    /**
     * @param BuilderEvent $event
     */
    public function onPostBuild(BuilderEvent $event)
    {
        $builder = $event->getBuilder();
        $builder->addEventListener(FormEvents::SUBMIT, array($this, 'onSubmit'));
    }

    /**
     * @param FormEvent $event
     */
    public function onSubmit(FormEvent $event)
    {
        /** @var Content $content */
        $content = $event->getData();
        $form = $event->getForm();

        foreach ($form->all() as $field) {
            $fieldName = $field->getName();
            $config = $field->getConfig();
            $type = $config->getType();

            if ($type->getName() == 'integrated_tinymce') {
                $repository = $this->documentManager->getRepository('IntegratedCommentBundle:CommentContent');

                $getter = Inflector::camelize('get_'.$fieldName);
                $setter = Inflector::camelize('set_'.$fieldName);

                $body = $content->$getter();

                if ($commentContent = $repository->findOneBy(['content.$id' => $content->getId(), 'field' => $fieldName])) {
                    $commentContent->setBody($body);
                    $this->documentManager->flush();
                } else {
                    $commentContent = new CommentContent();
                    $commentContent->setContent($content);
                    $commentContent->setField($fieldName);
                    $commentContent->setBody($body);
                    $this->documentManager->persist($commentContent);
                    $this->documentManager->flush();
                }

                $stripComments = $this->stripComments($body);
                $content->$setter($stripComments);
            }
        }
    }

    /**
     * @param $body
     * @return string
     */
    private function stripComments($body)
    {
        $domDocument = new \DOMDocument();
        $domDocument->loadHTML($body);

        $xpath = new \DOMXPath($domDocument);
        $nodeList = $xpath->query("//span[contains(@class, 'comment-added')]");

        /** @var \DOMElement $item */
        foreach ($nodeList as $item) {
            $itemHtml = $this->getInnerHtml($item);

            $newNode = $domDocument->createTextNode($itemHtml);

            $item->parentNode->replaceChild($newNode, $item);
        }

        $html = $domDocument->saveHTML($xpath->query('//body')->item(0));

        return preg_replace("/<\/?body>/i", "", $html);
    }

    /**
     * @param \DOMElement $domElement
     * @return string
     */
    private function getInnerHtml(\DOMElement $domElement)
    {
        $innerHTML = "";

        /** @var \DOMNode $child */
        foreach ($domElement->childNodes as $child) {
            $innerHTML .= $domElement->ownerDocument->saveHTML($child);
        }

        return $innerHTML;
    }
}
