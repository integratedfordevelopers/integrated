<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ChannelBundle;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
final class IntegratedChannelEvents
{
    /**
     * The CONFIG_CREATE_REQUEST event is fired when a new config is created.
     *
     * This event allows for the request to be intercepted before any mutations are made
     * in the controller.
     *
     * @Event("Integrated\Bundle\ChannelBundle\Event\GetResponseConfigEvent")
     */
    public const CONFIG_CREATE_REQUEST = 'integrated_channel.config.create.request';

    /**
     * The CONFIG_CREATE_SUBMITTED event is fired after the form is successfully submitted.
     *
     * This event allows the config to be edited before its persisted. It also allows for a
     * different response to be set instead of the default one.
     *
     * @Event("Integrated\Bundle\ChannelBundle\Event\FormConfigEvent")
     */
    public const CONFIG_CREATE_SUBMITTED = 'integrated_channel.config.create.submitted';

    /**
     * The CONFIG_CREATE_RESPONSE event is fired after the config is persisted.
     *
     * This even allows the access to the response just before it sent.
     *
     * @Event("Integrated\Bundle\ChannelBundle\Event\FilterResponseConfigEvent")
     */
    public const CONFIG_CREATE_RESPONSE = 'integrated_channel.config.create.response';

    /**
     * The CONFIG_EDIT_REQUEST event is fired when a config is edited.
     *
     * This event allows for the request to be intercepted before any mutations are made
     * in the controller.
     *
     * @Event("Integrated\Bundle\ChannelBundle\Event\GetResponseConfigEvent")
     */
    public const CONFIG_EDIT_REQUEST = 'integrated_channel.config.edit.request';

    /**
     * The CONFIG_EDIT_SUBMITTED event is fired after the form is successfully submitted.
     *
     * This event allows the config to be edited before its persisted. It also allows for a
     * different response to be set instead of the default one.
     *
     * @Event("Integrated\Bundle\ChannelBundle\Event\FormConfigEvent")
     */
    public const CONFIG_EDIT_SUBMITTED = 'integrated_channel.config.edit.submitted';

    /**
     * The CONFIG_EDIT_RESPONSE event is fired after the config is persisted.
     *
     * This even allows the access to the response just before it sent.
     *
     * @Event("Integrated\Bundle\ChannelBundle\Event\FilterResponseConfigEvent")
     */
    public const CONFIG_EDIT_RESPONSE = 'integrated_channel.config.edit.response';

    /**
     * The CONFIG_DELETE_REQUEST event is fired when a config is deleted.
     *
     * This event allows for the request to be intercepted before any mutations are made
     * in the controller.
     *
     * @Event("Integrated\Bundle\ChannelBundle\Event\GetResponseConfigEvent")
     */
    public const CONFIG_DELETE_REQUEST = 'integrated_channel.config.delete.request';

    /**
     * The CONFIG_DELETE_RESPONSE event is fired after the config is deleted.
     *
     * This even allows the access to the response just before it sent.
     *
     * @Event("Integrated\Bundle\ChannelBundle\Event\FilterResponseConfigEvent")
     */
    public const CONFIG_DELETE_RESPONSE = 'integrated_channel.config.delete.response';
}
