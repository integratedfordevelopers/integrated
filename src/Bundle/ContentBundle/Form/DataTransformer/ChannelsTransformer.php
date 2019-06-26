<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ChannelsTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        $result = [
            'options' => null,
            'defaults' => [],
        ];

        if ($value === null || $value === '') {
            return $result;
        }

        if (isset($value['disabled'])) {
            $result['options'] = 'disabled';

            switch ((int) $value['disabled']) {
                case 0:
                    $result['options'] = '';
                    break;

                case 1:
                    $result['options'] = 'hidden';
                    break;
            }
        }

        $defaults = [];
        if (isset($value['defaults'])) {
            // TODO filter out invalid channels

            foreach ($value['defaults'] as $channel) {
                $defaults[$channel['id']]['selected'] = true;
                $defaults[$channel['id']]['restrict'] = isset($channel['restrict']) ? (bool) $channel['restrict'] : false;
                $defaults[$channel['id']]['enforce'] = isset($channel['enforce']) ? (bool) $channel['enforce'] : false;
            }
        }

        if (isset($value['restricted'])) {
            // TODO filter out invalid channels

            foreach ($value['restricted'] as $channel) {
                $defaults[$channel]['restrict'] = true;
            }
        }
        $result['defaults'] = $defaults;

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        $result = [
            'disabled' => 0,
            'defaults' => [],
            'restricted' => [],
        ];

        if ($value === null || $value === '') {
            return $result;
        }

        switch ($value['options']) {
            case 'hidden':
                $result['disabled'] = 1;
                break;

            case 'disabled':
                $result['disabled'] = 2;
                break;
        }

        foreach ($value['defaults'] as $id => $options) {
            if ($options['selected']) {
                $result['defaults'][$id] = [
                    'id' => $id,
                    'restrict' => $options['restrict'] ? true : false,
                    'enforce' => $options['enforce'] ? true : false,
                ];
            }
            if ($options['restrict'] == true) {
                $result['restricted'][] = $id;
            }
        }

        $result['defaults'] = array_values($result['defaults']);

        return $result;
    }
}
