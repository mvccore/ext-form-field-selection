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
implements	\MvcCore\Ext\Forms\Fields\IMultiple,
			\MvcCore\Ext\Forms\Fields\IOptions
{
	use \MvcCore\Ext\Forms\Field\Props\Multiple;
	use \MvcCore\Ext\Forms\Field\Props\Options;

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
	 * Field specific values (camel case) and their validator default values.
	 * @var array
	 */
	protected static $fieldSpecificProperties = [
		'multiple'	=> NULL, 
		'options'	=> NULL, 
	];

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
		$flattenOptions = self::GetFlattenOptions($this->options);
		foreach ($rawSubmittedValues as & $rawSubmittedValueItem) {
			$rawSubmittedValueItemStr = (string) $rawSubmittedValueItem;
			if (array_key_exists($rawSubmittedValueItemStr, $flattenOptions)) {
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
