<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Solarium\Core\Plugin\Plugin;
use Solarium\Core\Event\Events;
use Solarium\Core\Event\PreExecuteRequest;
use Solarium\Core\Event\PostExecuteRequest;

/**
 * Based on NelmioSolariumBundle (https://github.com/nelmio/NelmioSolariumBundle)
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class SolariumDataCollector extends Plugin implements DataCollectorInterface, \Serializable
{
    /**
     * @var array
     */
    protected $data = array();

    /**
     * @var float
     */
    protected $startTime;

    /**
     * {@inheritdoc}
     */
    protected function initPluginType()
    {
        $dispatcher = $this->client->getEventDispatcher();
        $dispatcher->addListener(Events::PRE_EXECUTE_REQUEST, array($this, 'preExecuteRequest'), 1000);
        $dispatcher->addListener(Events::POST_EXECUTE_REQUEST, array($this, 'postExecuteRequest'), -1000);
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $time = 0;

        foreach ($this->getQueries() as $query) {
            $time += $query['duration'];
        }

        $this->data['total_time'] = $time;
    }

    /**
     * @param PreExecuteRequest $event
     */
    public function preExecuteRequest(PreExecuteRequest $event)
    {
        $this->startTime = microtime(true);
    }

    /**
     * @param PostExecuteRequest $event
     */
    public function postExecuteRequest(PostExecuteRequest $event)
    {
        $this->data['queries'][] = array(
            'request'  => $event->getRequest(),
            'response' => $event->getResponse(),
            'duration' => microtime(true) - $this->startTime,
            'base_uri' => $event->getEndpoint()->getBaseUri(),
        );
    }

    /**
     * @return array
     */
    public function getQueries()
    {
        return isset($this->data['queries']) ? $this->data['queries'] : array();
    }

    /**
     * @return int
     */
    public function getQueryCount()
    {
        return count($this->getQueries());
    }

    /**
     * @return int
     */
    public function getTotalTime()
    {
        return isset($this->data['total_time']) ? $this->data['total_time'] : 0;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'solr';
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($data)
    {
        $this->data = unserialize($data);
    }
}
