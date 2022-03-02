<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Routing;

use Integrated\Common\Content\ContentInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGenerator as SymfonyUrlGenerator;
use Symfony\Component\Routing\RequestContext;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class UrlGenerator implements UrlGeneratorInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $generator;

    /**
     * @var RequestContext
     */
    private $context;

    /**
     * @param UrlGeneratorInterface $genrator
     */
    public function __construct(UrlGeneratorInterface $genrator)
    {
        $this->generator = $genrator;
    }

    /**
     * {@inheritdoc}
     */
    public function setContext(RequestContext $context)
    {
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH)
    {
        if ($name instanceof ContentInterface) {
            return $this->doGenerate($name, $parameters, $referenceType);
        }

        $this->generator->setContext($this->context);

        return $this->generator->generate($name, $parameters, $referenceType);
    }

    /**
     * @param ContentInterface $content
     * @param array            $parameters
     * @param int              $referenceType
     *
     * @return string
     */
    protected function doGenerate(ContentInterface $content, array $parameters, $referenceType)
    {
        $url = sprintf(
            '%s/content/%s/%s',
            $this->context->getBaseUrl(),
            strtolower($content->getContentType()),
            $content->getSlug()
        );

        if ($parameters && $query = http_build_query($parameters, '', '&', \PHP_QUERY_RFC3986)) {
            $url .= '?'.strtr($query, ['%2F' => '/']);
        }

        if ($referenceType === self::ABSOLUTE_PATH) {
            return $url;
        }

        if ($referenceType === self::RELATIVE_PATH) {
            return SymfonyUrlGenerator::getRelativePath($this->context->getPathInfo(), $url);
        }

        $scheme = $this->context->getScheme();
        $port = '';

        if ('http' === $scheme && 80 != $this->context->getHttpPort()) {
            $port = ':'.$this->context->getHttpPort();
        } elseif ('https' === $scheme && 443 != $this->context->getHttpsPort()) {
            $port = ':'.$this->context->getHttpsPort();
        }

        $url = '//'.$this->context->getHost().$port.$url;

        if ($referenceType === self::ABSOLUTE_URL) {
            return "$scheme:".$url;
        }

        return $url;
    }
}
