<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
final class Events
{
	/**
	 *
	 */
	const PRE_EXECUTE			= 'integrated.solr.preExecute';

	/**
	 *
	 */
	const POST_EXECUTE			= 'integrated.solr.postExecute';

	/**
	 *
	 */
	const BATCHING				= 'integrated.solr.batching';

	/**
	 *
	 */
	const SENDING				= 'integrated.solr.sending';

	/**
	 *
	 */
	const RESULTS				= 'integrated.solr.results';

	/**
	 *
	 */
	const PROCESSED				= 'integrated.solr.processed';

	/**
	 *
	 */
	const ERROR					= 'integrated.solr.error';
} 