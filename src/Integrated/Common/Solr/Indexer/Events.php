<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Indexer;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
final class Events
{
    /**
     * This event is fired just before the indexing run is started.
     *
     * The event listener receives a IndexerEvent instance.
     *
     * @var string
     */
    const PRE_EXECUTE  = 'integrated.solr.preExecute';

    /**
     * This event is fired just before the indexing run is finished.
     *
     * The event listener receives a IndexerEvent instance.
     *
     * @var string
     */
    const POST_EXECUTE = 'integrated.solr.postExecute';

    /**
     * This event is fired just before a batch operation is added to
     * the batch. The command in the batch operation can changed or
     * removed. If the command is removed then if will not be batch
     * and the job will just be removed from the queue without further
     * action.
     *
     * The event listener receives a BatchEvent instance.
     *
     * @var string
     */
    const BATCHING     = 'integrated.solr.batching';

    /**
     * This event is fired just before the query is send to the solr
     * server. This will give the listener to change to change the query.
     *
     * The event listener receives a SendEvent instance.
     *
     * @var string
     */
    const SENDING      = 'integrated.solr.sending';

    /**
     * This event is fired after the query is send to the the solr
     * server. It contains the result of the query just send.
     *
     * The event listener receives a ResultEvent instance.
     *
     * @var string
     */
    const RESULTS      = 'integrated.solr.results';

    /**
     * This event is fired after a job has been successfully handled
     * and is removed from the queue.
     *
     * The event listener receives a MessageEvent instance.
     *
     * @var string
     */
    const PROCESSED    = 'integrated.solr.processed';

    /**
     * This event is fired after a error occurred with converting a job to
     * a command. The processing of the job will be halted after this and
     * the job will be removed from the queue.
     *
     * The event listener receives a ErrorEvent instance.
     *
     * @var string
     */
    const ERROR        = 'integrated.solr.error';
}
