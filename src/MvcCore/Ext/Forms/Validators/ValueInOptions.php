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
		$submittedValue = $this->getSubmittedValueCorrectType($rawSubmittedValue);
		if (
			($this->multiple && count($submittedValue) === 0) ||
			(!$this->multiple && $submittedValue === NULL)
		) return $submittedValue;
		$result = $this->completeSafeValueByOptions($submittedValue);
		if (
			($this->multiple && count($result) !== count($submittedValue)) ||
			(!$this->multiple && $result === NULL && $rawSubmittedValue !== NULL)
		) 
			$this->field->AddValidationError(
				static::GetErrorMessage(self::ERROR_VALID_OPTION)
			);
		return $result;
	}

	/**
	 * Return safe value(s), which exist(s) in field options 
	 * and return boolean (`TRUE`) if result is array or not.
	 * Example: `list($safeValue, $multiple) = $this->completeSafeValueByOptions($submittedValue);`;
	 * @param string|\string[] $submittedValue 
	 * @return array
	 */
	protected function completeSafeValueByOptions ($submittedValue) {
		$flattenOptions = self::GetFlattenOptions($this->options);
		if ($this->multiple) {
			$result = [];
			foreach ($submittedValue as & $submittedValueItem) {
				$submittedValueItemStr = strval($submittedValueItem);
				if (array_key_exists($submittedValueItemStr, $flattenOptions)) 
					$result[] = $submittedValueItemStr;
			}
		} else {
			$result = NULL;
			$submittedValueStr = strval($submittedValue);
			if (array_key_exists($submittedValueStr, $flattenOptions)) 
				$result = $submittedValueStr;
		}
		return $result;
	}

	/**
	 * @param string|\string[]|NULL $rawSubmittedValue 
	 * @return string|\string[]|NULL
	 */
	protected function getSubmittedValueCorrectType ($rawSubmittedValue) {
		$rawSubmittedValueIsArray = is_array($rawSubmittedValue);
		if ($this->multiple) {
			if ($rawSubmittedValueIsArray) {
				return $rawSubmittedValue;
			} else if ($rawSubmittedValue === NULL) {
				return [];
			} else {
				$rawSubmittedValue = trim((string) $rawSubmittedValue);
				return mb_strlen($rawSubmittedValue) > 0 
					? [$rawSubmittedValue] 
					: [];
			}
		} else {
			if ($rawSubmittedValueIsArray) {
				return isset($rawSubmittedValue[0]) && mb_strlen(trim((string) $rawSubmittedValue[0])) > 0
					? $rawSubmittedValue[0]
					: NULL;
			} else if ($rawSubmittedValue === NULL) {
				return NULL;
			} else {
				$rawSubmittedValue = trim((string) $rawSubmittedValue);
				return mb_strlen($rawSubmittedValue) > 0 
					? $rawSubmittedValue
					: NULL;
			}
		}
	}
}
