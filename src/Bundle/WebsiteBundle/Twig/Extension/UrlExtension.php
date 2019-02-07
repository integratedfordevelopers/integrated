<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WebsiteBundle\Twig\Extension;

use Integrated\Bundle\PageBundle\Services\SolrUrlExtractor;
use Integrated\Bundle\PageBundle\Services\UrlResolver;
use Integrated\Common\Content\ContentInterface;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class UrlExtension extends \Twig_Extension
{
    /**
     * @var UrlResolver
     */
    protected $urlResolver;

    /**
     * @var SolrUrlExtractor
     */
    protected $solrUrlExtractor;

    /**
     * @param UrlResolver      $urlResolver
     * @param SolrUrlExtractor $solrUrlExtractor
     */
    public function __construct(UrlResolver $urlResolver, SolrUrlExtractor $solrUrlExtractor)
    {
        $this->urlResolver = $urlResolver;
        $this->solrUrlExtractor = $solrUrlExtractor;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('integrated_url', [$this, 'getUrl']),
        ];
    }

    /**
     * @param mixed $document
     *
     * @return string|null
     */
    public function getUrl($document)
    {
        if ($document instanceof ContentInterface) {
            return $this->urlResolver->generateUrl($document);
        }

        //probably solr document
        return $this->solrUrlExtractor->getUrl($document);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_page_url';
    }
}
