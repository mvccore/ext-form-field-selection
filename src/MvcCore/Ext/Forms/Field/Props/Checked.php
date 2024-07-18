<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flidr (https://github.com/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/5.0.0/LICENSE.md
 */

namespace MvcCore\Ext\Forms\Field\Props;

/**
 * Trait for classes:
 * - `\MvcCore\Ext\Forms\Fields\Checkbox`
 * Trait contains protected property `checked` with it's getter and setter
 * and public static method to recognize `checked` boolean automatically from 
 * given field `$value`.
 * @mixin \MvcCore\Ext\Forms\Field
 */
trait Checked {

	/**
	 * If `TRUE`, field will be rendered as checked, `FALSE` otherwise.
	 * If not set, checked flag will be automatically resolved by field value
	 * with method `static::GetCheckedByValue($checkbox->GetValue());`
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-checked
	 * @var bool|NULL
	 */
	protected $checked = NULL;

	/**
	 * If `TRUE` (default `FALSE`), then there is always return boolean 
	 * type value after submit and there is rendered value attribute in HTML 
	 * element always with `true` string.
	 * @var bool
	 */
	protected $boolMode = FALSE;

	/**
	 * Set `TRUE` to rendered field as checked, `FALSE` otherwise.
	 * If not set, checked flag will be automatically resolved by field value
	 * with method `static::GetCheckedByValue($checkbox->GetValue());`
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-checked
	 * @param  bool $checked 
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetChecked ($checked = TRUE) {
		$this->checked = $checked;
		return $this;
	}

	/**
	 * Get `TRUE` if field is rendered as checked, `FALSE` otherwise.
	 * If not set, checked flag will be automatically resolved by field value
	 * with method `static::GetCheckedByValue($checkbox->GetValue());`
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-checked
	 * @return bool|NULL
	 */
	public function GetChecked () {
		return $this->checked;
	}
	
	/**
	 * Set `TRUE` (default `FALSE`) to always return boolean 
	 * type value after submit and to render value attribute in HTML 
	 * element always with `true` string.
	 * @param  bool $boolMode 
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetBoolMode ($boolMode = TRUE) {
		$this->boolMode = $boolMode;
		return $this;
	}

	/**
	 * Get `TRUE` (default `FALSE`) to always return boolean 
	 * type value after submit and to render value attribute in HTML 
	 * element always with `true` string.
	 * @return bool
	 */
	public function GetBoolMode () {
		return $this->boolMode;
	}

	/**
	 * Return `TRUE` for any `array`, `object`, `resource` or `unknown type`,
	 * `TRUE` for `boolean` `TRUE`, for `string` not equal to `no`, 
	 * for `integer` not equal to `0` and `TRUE` for `float` not equal to `0.0`.
	 * @param  mixed $value 
	 * @return bool
	 */
	public static function GetCheckedByValue ($value) {
		if ($value === NULL) 
			return FALSE;
		$checked = TRUE;
		if (is_bool($value) && $value === FALSE) {
			$checked = FALSE;
		} else if (is_string($value)) {
			$lowerValue = strtolower($value);
			if ($lowerValue == 'false' || $lowerValue == 'no' || $lowerValue == '') 
				$checked = FALSE;
		} else if (is_int($value) && $value === 0) {
			$checked = FALSE;
		} else if (is_float($value)) {
			$floatEpsilon = defined('PHP_FLOAT_EPSILON')
				? PHP_FLOAT_EPSILON
				: floatval('2.220446049250313E-16');
			if (abs($value - 0.0) < $floatEpsilon)
				$checked = FALSE;
		}
		return $checked;
	}

	/**
	 * Set up opposite label side than all other fields has.
	 * Set up render mode by form default fields render mode.
	 * @return void
	 */
	protected function preDispatchChecked () {
		/** @var $this \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\Field\Props\TabIndex */

		// set opposite label side than default from form.
		if ($this->labelSide === NULL) {
			$labelSideDefault = $this->form->GetFieldsLabelSideDefault();
			$this->labelSide = $labelSideDefault === \MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT
				? \MvcCore\Ext\Forms\IField::LABEL_SIDE_RIGHT
				: \MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT;
		}

		// never set up render mode no label for checkbox:
		if ($this->renderMode === NULL) {
			$formDefaultFieldRenderMode = $this->form->GetFieldsRenderModeDefault();
			if ($formDefaultFieldRenderMode === \MvcCore\Ext\IForm::FIELD_RENDER_MODE_LABEL_AROUND) {
				$this->renderMode = \MvcCore\Ext\IForm::FIELD_RENDER_MODE_LABEL_AROUND;
			} else if ($this->renderMode !== \MvcCore\Ext\IForm::FIELD_RENDER_MODE_LABEL_AROUND) {
				$this->renderMode = \MvcCore\Ext\IForm::FIELD_RENDER_MODE_NORMAL;
			}
		}
	}
}
