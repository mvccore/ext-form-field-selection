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

namespace MvcCore\Ext\Forms\Validators;

/**
 * Responsibility: Validate if submitted string(s) are presented in options keys.
 */
class		ValueInOptions 
extends		\MvcCore\Ext\Forms\Validator
implements	\MvcCore\Ext\Forms\Fields\IMultiple
{
	use \MvcCore\Ext\Forms\Field\Props\Multiple;

	/**
	 * Error message index(es).
	 * @var int
	 */
	const ERROR_VALID_OPTION = 0;

	/**
	 * Validation failure message template definitions.
	 * @var array
	 */
	protected static $errorMessages = [
		self::ERROR_VALID_OPTION	=> "Field '{0}' requires a valid option.",
	];

	/**
	 * Field has to implement for this validator two methods:
	 * - `GetAllOptionsKeys()` - To get all keys from options array as `\string[]`
	 * - `GetMultiple()` - To get boolean flag if there could be more submitted options or not.
	 * @var \MvcCore\Ext\Forms\Fields\IOptions
	 */
	protected $field = NULL;

	/**
	 * Set up field instance, where is validated value by this 
	 * validator durring submit before every `Validate()` method call.
	 * Check if given field implements `\MvcCore\Ext\Forms\Fields\IOptions`
	 * and `\MvcCore\Ext\Forms\Fields\IMultiple`.
	 * @param \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm $form 
	 * @return \MvcCore\Ext\Forms\Validator|\MvcCore\Ext\Forms\IValidator
	 */
	public function & SetField (\MvcCore\Ext\Forms\IField & $field) {
		if (!$field instanceof \MvcCore\Ext\Forms\Fields\IOptions) 
			$this->throwNewInvalidArgumentException(
				'If field has configured `ValueInOptions` validator, it has to implement '
				.'interface `\\MvcCore\\Ext\\Forms\\Fields\\IOptions`.'
			);
		if (!$field instanceof \MvcCore\Ext\Forms\Fields\IMultiple) 
			$this->throwNewInvalidArgumentException(
				'If field has configured `ValueInOptions` validator, it has to implement '
				.'interface `\\MvcCore\\Ext\\Forms\\Fields\\IMultiple`.'
			);

		$fieldMultiple = $field->GetMultiple();
		if ($fieldMultiple !== NULL) {
			// if validator is added as string - get multiple property from field:
			$this->multiple = $fieldMultiple;
		} else if ($this->multiple !== NULL && $fieldMultiple === NULL) {
			// if this validator is added into field as instance - check field if it has multiple attribute defined:
			$field->SetMultiple($this->multiple);
		}
		
		return parent::SetField($field);
	}

	/**
	 * Return array with only submitted values from options keys
	 * or return string which exists as key in options or `NULL` 
	 * if submitted value is `NULL`. Add error if submitted value 
	 * is not the same as value after existence check.
	 * @param string|array			$submitValue
	 * @return string|\string[]|NULL	Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		list($result, $multiple) = $this->completeSafeValueByOptions($rawSubmittedValue);
		if (
			($multiple && count($result) !== count($rawSubmittedValue)) ||
			(!$multiple && $result === NULL)
		) {
			$this->field->AddValidationError(
				static::GetErrorMessage(self::ERROR_VALID_OPTION)
			);
		}
		return $result;
	}

	/**
	 * Return safe value(s), which exist(s) in field options 
	 * and return boolean (`TRUE`) if result is array or not.
	 * Example: `list($safeValue, $multiple) = $this->completeSafeValueByOptions($rawSubmittedValue);`;
	 * @param string|array $rawSubmittedValue 
	 * @return array
	 */
	protected function completeSafeValueByOptions ($rawSubmittedValue) {
		$result = $this->multiple ? [] : NULL;
		$rawSubmittedValueArrayType = gettype($rawSubmittedValue) == 'array';
		if ($rawSubmittedValueArrayType) {
			if ($this->multiple) {
				$rawSubmittedValues = $rawSubmittedValue;
			} else {
				$rawSubmittedValue = (string) $rawSubmittedValue;
				$rawSubmittedValues = mb_strlen($rawSubmittedValue) > 0 
					? [$rawSubmittedValue] 
					: [];
			}
		} else {
			$rawSubmittedValue = (string) $rawSubmittedValue;
			$rawSubmittedValues = mb_strlen($rawSubmittedValue) > 0 
				? [$rawSubmittedValue] 
				: [];
		}
		$allOptionKeys = $this->field->GetAllOptionsKeys();
		foreach ($rawSubmittedValues as & $rawSubmittedValueItem) {
			$rawSubmittedValueItemStr = (string) $rawSubmittedValueItem;
			if (in_array($rawSubmittedValueItem, $allOptionKeys)) {
				if ($this->multiple) {
					$result[] = $rawSubmittedValueItemStr;
				} else {
					$result = $rawSubmittedValueItemStr;
				}
				if (!$this->multiple) break;
			}
		}
		return [
			$result, 
			$this->multiple
		];
	}
}
