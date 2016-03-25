<?php

namespace Integrated\Bundle\CommentBundle\Service;

use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentBundle\Document\Content\Article;

/**
 * Class ContentCommentTransformer
 */
class ContentCommentTransformer
{
    /**
     * @var DocumentManager
     */
    protected $documentManager;

    /**
     * ContentCommentTransformer constructor.
     * @param DocumentManager $documentManager
     */
    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    /**
     * @param Article $article
     */
    public function parseComments(Article $article)
    {
        $content = $article->getContent();

        $domDocument = new \DOMDocument();
        $domDocument->loadHTML($content);

        $xpath = new \DOMXPath($domDocument);
        $nodeList = $xpath->query("//span[contains(@class, 'comment-added')]");

        /** @var \DOMElement $item */
        foreach ($nodeList as $item) {
            $commentId = $item->getAttribute('data-id');

            $itemHtml = $this->getInnerHtml($item);

            $newNode = $domDocument->createTextNode($itemHtml);

            $item->parentNode->replaceChild($newNode, $item);

            $path = $newNode->getNodePath();
        }

        $outputContent = $domDocument->saveHTML($xpath->query('//body')->item(0));

        $doc = new \DOMDocument();
        $doc->loadHTML($outputContent);
        $xpath2 = new \DOMXPath($doc);
        $test = $xpath2->query($path);

    }

    /**
     * @param Article $article
     */
    public function setComments(Article $article)
    {

    }

    private function getInnerHtml(\DOMElement $domElement)
    {
        $innerHTML = "";

        /** @var \DOMNode $child */
        foreach ($domElement->childNodes as $child)
        {
            $innerHTML .= $domElement->ownerDocument->saveHTML($child);
        }

        return $innerHTML;

    }

}