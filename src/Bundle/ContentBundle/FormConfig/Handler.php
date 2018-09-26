<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\FormConfig;

use Integrated\Bundle\ContentBundle\FormConfig\Util\KeyGenerator;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\FormConfig\Exception\InvalidArgumentException;
use Integrated\Common\FormConfig\FormConfigEditableInterface;
use Integrated\Common\FormConfig\FormConfigFactoryInterface;
use Integrated\Common\FormConfig\FormConfigManagerInterface;

class Handler
{
    /**
     * @var FormConfigManagerInterface
     */
    private $manager;

    /**
     * @var FormConfigFactoryInterface
     */
    private $factory;

    /**
     * @var KeyGenerator
     */
    private $generator;

    /**
     * @param FormConfigManagerInterface $manager
     * @param FormConfigFactoryInterface $factory
     * @param KeyGenerator               $generator
     */
    public function __construct(
        FormConfigManagerInterface $manager,
        FormConfigFactoryInterface $factory,
        KeyGenerator $generator = null
    ) {
        $this->manager = $manager;
        $this->factory = $factory;
        $this->generator = $generator ?: new KeyGenerator();
    }

    /**
     * @param ContentTypeInterface $type
     * @param string               $key
     * @param array                $data
     */
    public function handle(ContentTypeInterface $type, ?string $key, array $data): void
    {
        if ($key === null) {
            $key = $this->generator->generate($data['name']);
        }

        $config = null;

        if ($this->manager->has($type, $key)) {
            $config = $this->manager->get($type, $key);

            if (!$config instanceof FormConfigEditableInterface) {
                throw new InvalidArgumentException('The config is not editable.');
            }
        } else {
            $config = $this->factory->create($type, $key);
        }

        $config->setName($data['name']);
        $config->setFields($data['fields']);

        $this->manager->save($config);
    }
}
