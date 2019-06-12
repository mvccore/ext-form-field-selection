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
	 * Field specific values (camel case) and their validator default values.
	 * @var array
	 */
	protected static $fieldSpecificProperties = [
		'minOptions'	=> NULL, 
		'maxOptions'	=> NULL, 
	];

	/**
	 * Set up field instance, where is validated value by this 
	 * validator during submit before every `Validate()` method call.
	 * This method is also called once, when validator instance is separately 
	 * added into already created field instance to process any field checking.
	 * @param \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField $field 
	 * @return \MvcCore\Ext\Forms\Validator|\MvcCore\Ext\Forms\IValidator
	 */
	public function & SetField (\MvcCore\Ext\Forms\IField & $field) {
		/** @var $this \MvcCore\Ext\Forms\IValidator */
		$this->field = & $field;
		$this->setUpFieldProps(array_merge(
			self::$fieldSpecificProperties,
			parent::$fieldSpecificProperties
		));
		return $this;
	}
	
	/**
	 * Validate raw user input with maximum options count check.
	 * @param string|array $rawSubmittedValue Raw submitted value from user.
	 * @return \string[]|NULL Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		$rawSubmittedArr = [];
		if (is_array($rawSubmittedValue)) {
			$rawSubmittedArr = $rawSubmittedValue;
		} else if (is_string($rawSubmittedValue) && mb_strlen($rawSubmittedValue) > 0) {
			$rawSubmittedArr = [$rawSubmittedValue];
		}
		$submittedArrCount = count($rawSubmittedArr);
		if ($submittedArrCount === 0) return [];

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
