<?php

/**
 * @author Damian Dlugosz <d.dlugosz@bestnetwork.it>
 */

namespace Integrated\Bundle\FormTypeBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BootstrapButtonExtension extends AbstractExtension
{
    /**
     * @var BootstrapIconExtension
     */
    private $iconExtension;

    private $defaults = [
        'label' => '',
        'icon' => false,
        'type' => 'default',
        'size' => 'md',
        'attr' => [],
    ];

    /**
     * @param BootstrapIconExtension $iconExtension
     */
    public function __construct(BootstrapIconExtension $iconExtension)
    {
        $this->iconExtension = $iconExtension;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('button', [$this, 'buttonFunction'], ['is_safe' => ['html']]),
            new TwigFunction('button_link', [$this, 'buttonLinkFunction'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param array $options
     *
     * @return string
     */
    public function buttonFunction(array $options = [])
    {
        $options = array_merge($this->defaults, $options);

        $options['attr']['class'] = "btn btn-{$options['type']} btn-{$options['size']}".(isset($options['attr']['class']) ? ' '.$options['attr']['class'] : '');
        $options['attr']['type'] = isset($options['submit']) && $options['submit'] ? 'submit' : 'button';

        $icon = $options['icon'] ? $this->iconExtension->iconFunction($options['icon']).' ' : '';
        $attr = $options['attr'] ? $this->attributes($options['attr']) : '';

        $button = "<button{$attr}>{$icon}{$options['label']}</button>";

        return $button;
    }

    /**
     * @param array $options
     *
     * @return string
     */
    public function buttonLinkFunction(array $options = [])
    {
        $options = array_merge($this->defaults, $options);

        $options['attr']['class'] = "btn btn-{$options['type']} btn-{$options['size']}".(isset($options['attr']['class']) ? ' '.$options['attr']['class'] : '');
        $options['attr']['href'] = (isset($options['url']) ? $options['url'] : '#');

        $icon = $options['icon'] ? $this->iconExtension->iconFunction($options['icon']).' ' : '';
        $attr = $options['attr'] ? $this->attributes($options['attr']) : '';

        $button = "<a{$attr}>{$icon}{$options['label']}</a>";

        return $button;
    }

    private function attributes(array $attributes)
    {
        $result = '';
        array_walk($attributes, function ($value, $attr) use (&$result) {
            $result .= " $attr=\"$value\"";
        });

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'braincrafted_bootstrap_button';
    }
}
