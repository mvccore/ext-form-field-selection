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
 * Responsibility: Validate minimum or maximum selected options count in 
 *                 submitted value by configured field setters.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class MinMaxOptions extends ValueInOptions {

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
	 * Create min./max. options validator instance.
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
	 * @param  int   $minOptions
	 * Minimum options count to select. Default value is `NULL` to not limit anything.
	 * @param  int   $maxOptions
	 * Maximum options count to select. Default value is `NULL` to not limit anything.
	 * 
	 * @throws \InvalidArgumentException 
	 * @return void
	 */
	public function __construct(
		array $cfg = [],
		$multiple = NULL,
		array $options = [],
		$minOptions = NULL,
		$maxOptions = NULL
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
	 * Set up field instance, where is validated value by this 
	 * validator during submit before every `Validate()` method call.
	 * This method is also called once, when validator instance is separately 
	 * added into already created field instance to process any field checking.
	 * @param  \MvcCore\Ext\Forms\Field $field 
	 * @return \MvcCore\Ext\Forms\Validator
	 */
	public function SetField (\MvcCore\Ext\Forms\IField $field) {
		/** @var \MvcCore\Ext\Forms\Validator $this */
		$this->field = $field;
		$this->setUpFieldProps(array_merge(
			self::$fieldSpecificProperties,
			parent::$fieldSpecificProperties
		));
		return $this;
	}
	
	/**
	 * Validate raw user input with maximum options count check.
	 * @param  string|array $rawSubmittedValue Raw submitted value from user.
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
