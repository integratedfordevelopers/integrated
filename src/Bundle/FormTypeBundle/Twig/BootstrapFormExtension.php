<?php
/**
 * This file is part of BraincraftedBootstrapBundle.
 * (c) 2012-2013 by Florian Eckerstorfer.
 */

namespace Integrated\Bundle\FormTypeBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * BootstrapFormExtension.
 *
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright  2012-2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 *
 * @see       http://bootstrap.braincrafted.com Bootstrap for Symfony2
 */
class BootstrapFormExtension extends AbstractExtension
{
    /** @var string */
    private $style;

    /** @var string */
    private $colSize = 'lg';

    /** @var int */
    private $widgetCol = 10;

    /** @var int */
    private $labelCol = 2;

    /** @var int */
    private $simpleCol = false;

    /** @var array */
    private $settingsStack = [];

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('bootstrap_set_style', [$this, 'setStyle']),
            new TwigFunction('bootstrap_get_style', [$this, 'getStyle']),
            new TwigFunction('bootstrap_set_col_size', [$this, 'setColSize']),
            new TwigFunction('bootstrap_get_col_size', [$this, 'getColSize']),
            new TwigFunction('bootstrap_set_widget_col', [$this, 'setWidgetCol']),
            new TwigFunction('bootstrap_get_widget_col', [$this, 'getWidgetCol']),
            new TwigFunction('bootstrap_set_label_col', [$this, 'setLabelCol']),
            new TwigFunction('bootstrap_get_label_col', [$this, 'getLabelCol']),
            new TwigFunction('bootstrap_set_simple_col', [$this, 'setSimpleCol']),
            new TwigFunction('bootstrap_get_simple_col', [$this, 'getSimpleCol']),
            new TwigFunction('bootstrap_backup_form_settings', [$this, 'backupFormSettings']),
            new TwigFunction('bootstrap_restore_form_settings', [$this, 'restoreFormSettings']),
            new TwigFunction(
                'checkbox_row',
                null,
                ['is_safe' => ['html'], 'node_class' => 'Symfony\Bridge\Twig\Node\SearchAndRenderBlockNode']
            ),
            new TwigFunction(
                'radio_row',
                null,
                ['is_safe' => ['html'], 'node_class' => 'Symfony\Bridge\Twig\Node\SearchAndRenderBlockNode']
            ),
            new TwigFunction(
                'global_form_errors',
                null,
                ['is_safe' => ['html'], 'node_class' => 'Symfony\Bridge\Twig\Node\SearchAndRenderBlockNode']
            ),
            new TwigFunction(
                'form_control_static',
                [$this, 'formControlStaticFunction'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'braincrafted_bootstrap_form';
    }

    /**
     * Sets the style.
     *
     * @param string $style Name of the style
     */
    public function setStyle($style)
    {
        $this->style = $style;
    }

    /**
     * Returns the style.
     *
     * @return string Name of the style
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * Sets the column size.
     *
     * @param string $colSize Column size (xs, sm, md or lg)
     */
    public function setColSize($colSize)
    {
        $this->colSize = $colSize;
    }

    /**
     * Returns the column size.
     *
     * @return string Column size (xs, sm, md or lg)
     */
    public function getColSize()
    {
        return $this->colSize;
    }

    /**
     * Sets the number of columns of widgets.
     *
     * @param int $widgetCol number of columns
     */
    public function setWidgetCol($widgetCol)
    {
        $this->widgetCol = $widgetCol;
    }

    /**
     * Returns the number of columns of widgets.
     *
     * @return int Number of columns.Class
     */
    public function getWidgetCol()
    {
        return $this->widgetCol;
    }

    /**
     * Sets the number of columns of labels.
     *
     * @param int $labelCol number of columns
     */
    public function setLabelCol($labelCol)
    {
        $this->labelCol = $labelCol;
    }

    /**
     * Returns the number of columns of labels.
     *
     * @return int number of columns
     */
    public function getLabelCol()
    {
        return $this->labelCol;
    }

    /**
     * Sets the number of columns of simple widgets.
     *
     * @param int $simpleCol number of columns
     */
    public function setSimpleCol($simpleCol)
    {
        $this->simpleCol = $simpleCol;
    }

    /**
     * Returns the number of columns of simple widgets.
     *
     * @return int number of columns
     */
    public function getSimpleCol()
    {
        return $this->simpleCol;
    }

    /**
     * Backup the form settings to the stack.
     *
     * @internal Should only be used at the beginning of form_start. This allows
     *           a nested subform to change its settings without affecting its
     *           parent form.
     */
    public function backupFormSettings()
    {
        $settings = [
            'style' => $this->style,
            'colSize' => $this->colSize,
            'widgetCol' => $this->widgetCol,
            'labelCol' => $this->labelCol,
            'simpleCol' => $this->simpleCol,
        ];

        $this->settingsStack[] = $settings;
    }

    /**
     * Restore the form settings from the stack.
     *
     * @internal should only be used at the end of form_end
     *
     * @see backupFormSettings
     */
    public function restoreFormSettings()
    {
        if (\count($this->settingsStack) < 1) {
            return;
        }

        $settings = array_pop($this->settingsStack);

        $this->style = $settings['style'];
        $this->colSize = $settings['colSize'];
        $this->widgetCol = $settings['widgetCol'];
        $this->labelCol = $settings['labelCol'];
        $this->simpleCol = $settings['simpleCol'];
    }

    /**
     * @param string $label
     * @param string $value
     *
     * @return string
     */
    public function formControlStaticFunction($label, $value)
    {
        return sprintf(
            '<div class="form-group"><label class="col-sm-%s control-label">%s</label><div class="col-sm-%s"><p class="form-control-static">%s</p></div></div>',
            $this->getLabelCol(), $label, $this->getWidgetCol(), $value
        );
    }
}
