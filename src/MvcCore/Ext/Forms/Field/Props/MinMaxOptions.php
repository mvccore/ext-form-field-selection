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
 * - `\MvcCore\Ext\Forms\Fields\Select`
 *    - `\MvcCore\Ext\Forms\Fields\CountrySelect`
 * - `\MvcCore\Ext\Forms\CheckboxGroup`
 * - `\MvcCore\Ext\Forms\Validators\MinMaxOptions`
 */
trait MinMaxOptions
{
	/**
	 * Minimum options count to select. 
	 * Default value is `NULL` to not limit anything.
	 * @var int|NULL
	 */
	protected $minOptions = NULL;

	/**
	 * Maximum options count to select. 
	 * Default value is `NULL` to not limit anything.
	 * @var int|NULL
	 */
	protected $maxOptions = NULL;

	/**
	 * Field is required bubble message for javascript.
	 * @var string|NULL
	 */
	protected $requiredBubbleMessage = NULL;

	/**
	 * Minimum options bubble message for javascript.
	 * @var string|NULL
	 */
	protected $minOptionsBubbleMessage = NULL;

	/**
	 * Maximum options bubble message for javascript.
	 * @var string|NULL
	 */
	protected $maxOptionsBubbleMessage = NULL;
	
	/**
	 * Get minimum options count to select. 
	 * Default value is `NULL` to not limit anything.
	 * @return int|NULL
	 */
	public function GetMinOptions () {
		return $this->minOptions;
	}
	
	/**
	 * Set minimum options count to select. 
	 * Default value is `NULL` to not limit anything.
	 * @param int|NULL $minOptions
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetMinOptions ($minOptions) {
		$this->minOptions = $minOptions;
		return $this;
	}

	/**
	 * Get maximum options count to select. 
	 * Default value is `NULL` to not limit anything.
	 * @return int|NULL
	 */
	public function GetMaxOptions () {
		return $this->maxOptions;
	}
	
	/**
	 * Set maximum options count to select. 
	 * Default value is `NULL` to not limit anything.
	 * @param int|NULL $maxOptions
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetMaxOptions ($maxOptions) {
		$this->maxOptions = $maxOptions;
		return $this;
	}

	/**
	 * Get field is required bubble message for javascript.
	 * This method could be used only for checkbox group control.
	 * @return string
	 */
	public function GetRequiredBubbleMessage () {
		return $this->requiredBubbleMessage;
	}

	/**
	 * Set field is required bubble message for javascript.
	 * This method could be used only for checkbox group control.
	 * @param string $requiredBubbleMessage 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetRequiredBubbleMessage ($requiredBubbleMessage) {
		$this->requiredBubbleMessage = $requiredBubbleMessage;
		return $this;
	}

	/**
	 * Get minimum options bubble message for javascript.
	 * @return string
	 */
	public function GetMinOptionsBubbleMessage () {
		return $this->minOptionsBubbleMessage;
	}

	/**
	 * Set minimum options bubble message for javascript.
	 * @param string $minOptionsBubbleMessage 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetMinOptionsBubbleMessage ($minOptionsBubbleMessage) {
		$this->minOptionsBubbleMessage = $minOptionsBubbleMessage;
		return $this;
	}

	/**
	 * Get maximum options bubble message for javascript.
	 * @return string
	 */
	public function GetMaxOptionsBubbleMessage () {
		return $this->maxOptionsBubbleMessage;
	}

	/**
	 * Set maximum options bubble message for javascript.
	 * @param string $minOptionsBubbleMessage 
	 * @return \MvcCore\Ext\Forms\Field|\MvcCore\Ext\Forms\IField
	 */
	public function & SetMaxOptionsBubbleMessage ($maxOptionsBubbleMessage) {
		$this->maxOptionsBubbleMessage = $maxOptionsBubbleMessage;
		return $this;
	}
	
	/**
	 * Check if field has proper min/max validator if any value for minimum 
	 * or maximum options count is set. Process this check immediately
	 * when field is added into form instance.
	 * @return void
	 */
	protected function setFormMinMaxOptions () {
		if (
			($this->minOptions !== NULL || $this->maxOptions !== NULL) &&
			!isset($this->validators['MinMaxOptions'])
		)
			$this->validators['MinMaxOptions'] = 'MinMaxOptions';
	}
}
