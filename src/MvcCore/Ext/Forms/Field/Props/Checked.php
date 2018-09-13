<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flídr (https://github.com/mvccore/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/4.0.0/LICENCE.md
 */

namespace MvcCore\Ext\Forms\Field\Props;

/**
 * Trait for classes:
 * - `\MvcCore\Ext\Forms\Fields\Checkbox`
 * Trait contains protected property `checked` with it's getter and setter
 * and public static method to recognize `checked` boolean automaticly from 
 * given field `$value`.
 */
trait Checked
{
	/**
	 * If `TRUE`, field will be rendered as checked, `FALSE` otherwise.
	 * If not set, checked flag will be automaticly resolved by field value
	 * with method `static::GetCheckedByValue($checkbox->GetValue());`
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-checked
	 * @var bool|NULL
	 */
	protected $checked = NULL;

	/**
	 * Set `TRUE` to rendered field as checked, `FALSE` otherwise.
	 * If not set, checked flag will be automaticly resolved by field value
	 * with method `static::GetCheckedByValue($checkbox->GetValue());`
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-checked
	 * @param bool $checked 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetChecked ($checked = TRUE) {
		$this->checked = $checked;
		return $this;
	}

	/**
	 * Get `TRUE` if field is rendered as checked, `FALSE` otherwise.
	 * If not set, checked flag will be automaticly resolved by field value
	 * with method `static::GetCheckedByValue($checkbox->GetValue());`
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-checked
	 * @return bool|NULL
	 */
	public function GetChecked () {
		return $this->checked;
	}

	/**
	 * Return `TRUE` for any `array`, `object`, `resource` or `unknown type`,
	 * `TRUE` for `boolean` `TRUE`, for `string` not equal to `no`, 
	 * for `integer` not equal to `0` and `TRUE` for `float` not equal to `0.0`.
	 * @param mixed $value 
	 * @return bool
	 */
	public static function GetCheckedByValue ($value) {
		if ($value === NULL) return FALSE;
		$checked = TRUE;
		if (is_bool($value) && $value === FALSE) {
			$checked = FALSE;
		} else if (is_string($value)) {
			$lowerValue = strtolower($value);
			if ($lowerValue == 'false' || $lowerValue == 'no') 
				$checked = FALSE;
		} else if (is_int($value) && $value === 0) {
			$checked = FALSE;
		} else if (is_float($value) && $value === 0.0) {
			$checked = FALSE;
		}
		return $checked;
	}
}
