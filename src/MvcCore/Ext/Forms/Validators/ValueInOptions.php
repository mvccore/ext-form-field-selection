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

namespace MvcCore\Ext\Forms\Validators;

/**
 * Responsibility: Validate if submitted string(s) are presented in options keys.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class		ValueInOptions
extends		\MvcCore\Ext\Forms\Validator
implements	\MvcCore\Ext\Forms\Fields\IMultiple,
			\MvcCore\Ext\Forms\Fields\IOptions {

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
		'multiple'		=> NULL,
		'options'		=> NULL,
	];
	

	/**
	 * Create value in options validator instance.
	 * 
	 * @param  array $cfg
	 * Config array with protected properties and it's 
	 * values which you want to configure, presented 
	 * in camel case properties names syntax.
	 * 
	 * @param  bool  $multiple
	 * If control is `<input>` with `type` as `file` or `email`,
	 * this Boolean attribute indicates whether the user can enter 
	 * more than one value.
	 * If control is `<input>` with `type` as `range`, there are 
	 * rendered two connected sliders (range controls) as one control
	 * to simulate range from and range to. Result value will be array.
	 * If control is `<select>`, this Boolean attribute indicates 
	 * that multiple options can be selected in the list. When 
	 * multiple is specified, most browsers will show a scrolling 
	 * list box instead of a single line drop down.
	 * @param  array $options
	 * Form group control options to render more sub-control attributes for specified
	 * submitted values (array keys). This property configuration is required.
	 * 
	 * @throws \InvalidArgumentException 
	 * @return void
	 */
	public function __construct(
		array $cfg = [],
		$multiple = NULL,
		array $options = []
	) {
		$errorMessages = static::$errorMessages;
		$this->consolidateCfg($cfg, func_get_args(), func_num_args());
		parent::__construct($cfg);
		if (self::$errorMessages !== $errorMessages)
			static::$errorMessages = array_replace(
				self::$errorMessages,
				$errorMessages
			);
	}

	/**
	 * Return array with only submitted values from options keys
	 * or return string which exists as key in options or `NULL`
	 * if submitted value is `NULL`. Add error if submitted value
	 * is not the same as value after existence check.
	 * @param  string|array          $rawSubmittedValue
	 * @return string|\string[]|\int|\int[]|\float|\float[]|NULL Safe submitted value or `NULL` if not possible to return safe value.
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
	 * @param  string|\string[]|\int|\int[]|\float|\float[]|NULL $submittedValue
	 * @return array
	 */
	protected function completeSafeValueByOptions ($submittedValue) {
		if ($this->field instanceof \MvcCore\Ext\Forms\Fields\IOptions) {
			/** @var \MvcCore\Ext\Forms\Fields\IOptions $optionsField */
			$optionsField = $this->field;
			$flattenOptions = $optionsField->GetFlattenOptions();
		} else {
			$flattenOptions = $this->GetFlattenOptions($this->options);
		}
		$flattenOptionsKeys = array_keys($flattenOptions);
		if ($this->multiple) {
			$result = [];
			foreach ($submittedValue as & $submittedValueItem) {
				if (array_key_exists($submittedValueItem, $flattenOptions)) {
					$keyPosition = array_search($submittedValueItem, $flattenOptionsKeys, FALSE);
					//$result[] = $submittedValueItem;
					// do not return submitted string but real option key type:
					$result[] = $flattenOptionsKeys[$keyPosition];
				}
			}
		} else {
			$result = NULL;
			if (array_key_exists($submittedValue, $flattenOptions)) {
				$keyPosition = array_search($submittedValue, $flattenOptionsKeys, FALSE);
				//$result = $submittedValue;
				// do not return submitted string but real option key type:
				$result = $flattenOptionsKeys[$keyPosition];
			}
		}
		return $result;
	}

	/**
	 * @param  string|\string[]|NULL $rawSubmittedValue
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
