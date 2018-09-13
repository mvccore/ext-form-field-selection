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
 * Responsibility: Validate minimum or maximum selected options count in 
 *				   submitted value by configured field setters.
 */
class MinMaxOptions extends ValueInOptions
{
	use \MvcCore\Ext\Forms\Field\Props\MinMaxOptions;
	
	/**
	 * Valid email address error message index.
	 * @var int
	 */
	const ERROR_MIN_OPTIONS = 1;
	const ERROR_MAX_OPTIONS = 2;

	/**
	 * Validation failure message template definitions.
	 * @var array
	 */
	protected static $errorMessages = [
		self::ERROR_MIN_OPTIONS	=> "Field '{0}' requires at least {1} chosen option(s) at minimum.",
		self::ERROR_MAX_OPTIONS	=> "Field '{0}' requires {1} of the selected option(s) at maximum.",
	];

	/**
	 * Set up field instance, where is validated value by this 
	 * validator durring submit before every `Validate()` method call.
	 * This method is also called once, when validator instance is separately 
	 * added into already created field instance to process any field checking.
	 * @param \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField $field 
	 * @return \MvcCore\Ext\Forms\Validator|\MvcCore\Ext\Forms\IValidator
	 */
	public function & SetField (\MvcCore\Ext\Forms\IField & $field) {
		parent::SetField($field);
		if (!$field instanceof \MvcCore\Ext\Forms\Fields\IMinMaxOptions) 
			$this->throwNewInvalidArgumentException(
				"Field doesn't implement interface `\\MvcCore\\Ext\\Forms\\Fields\\IMinMaxOptions`."
			);
		
		$fieldMinOptions = $field->GetMinOptions();
		if ($fieldMinOptions !== NULL) {
			// if validator is added as string - get min property from field:
			$this->minOptions = $fieldMinOptions;
		} else if ($this->minOptions !== NULL && $fieldMinOptions === NULL) {
			// if this validator is added into field as instance - check field if it has min attribute defined:
			$field->SetMinOptions($this->minOptions);
		}

		$fieldMaxOptions = $field->GetMaxOptions();
		if ($fieldMaxOptions !== NULL) {
			// if validator is added as string - get max property from field:
			$this->maxOptions = $fieldMaxOptions;
		} else if ($this->maxOptions !== NULL && $fieldMaxOptions === NULL) {
			// if this validator is added into field as instance - check field if it has max attribute defined:
			$field->SetMaxOptions($this->maxOptions);
		}

		return $this;
	}
	
	/**
	 * Validate raw user input with maximum options count check.
  * @param string|array $rawSubmittedValue Raw submitted value from user.
	 * @return string|NULL Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		$rawSubmittedArr = [];
		if (is_array($rawSubmittedValue)) {
			$rawSubmittedArr = $rawSubmittedValue;
		} else if (is_string($rawSubmittedValue) && mb_strlen($rawSubmittedValue) > 0) {
			$rawSubmittedArr = [$rawSubmittedValue];
		}
		$submittedArrCount = count($rawSubmittedArr);

		// check if there is enough options checked
		if ($this->minOptions !== NULL && $this->minOptions > 0 && $submittedArrCount < $this->minOptions) 
			$this->field->AddValidationError(
				static::GetErrorMessage(static::ERROR_MIN_OPTIONS),
				[$this->minOptions]
			);
		
		// check if there is not more options checked
		if ($this->maxOptions !== NULL && $this->maxOptions > 0 && $submittedArrCount > $this->maxOptions) {
			$rawSubmittedArr = array_slice($rawSubmittedArr, 0, $this->maxOptions);
			$this->field->AddValidationError(
				static::GetErrorMessage(static::ERROR_MAX_OPTIONS),
				[$this->maxOptions]
			);
		}

		return $rawSubmittedArr;
	}
}
