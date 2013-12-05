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
	 *
	 *
	 * @var string
	 */
	const PRE_EXECUTE			= 'integrated.solr.preExecute';

	/**
	 *
	 *
	 * @var string
	 */
	const POST_EXECUTE			= 'integrated.solr.postExecute';

	/**
	 *
	 *
	 * @var string
	 */
	const BATCHING				= 'integrated.solr.batching';

	/**
	 *
	 *
	 * @var string
	 */
	const SENDING				= 'integrated.solr.sending';

	/**
	 *
	 *
	 * @var string
	 */
	const RESULTS				= 'integrated.solr.results';

	/**
	 *
	 *
	 * @var string
	 */
	const PROCESSED				= 'integrated.solr.processed';

	/**
	 *
	 *
	 * @var string
	 */
	const ERROR					= 'integrated.solr.error';
} 