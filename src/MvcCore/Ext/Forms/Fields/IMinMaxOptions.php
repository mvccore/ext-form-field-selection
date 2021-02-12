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

namespace MvcCore\Ext\Forms\Fields;

/**
 * Responsibility: define getters and setters for field properties: 
 *                 `minOptions`, `maxOptions`, `minOptionsBubbleMessage` 
 *                 and `maxOptionsBubbleMessage`.
 * Interface for classes:
 * - `\MvcCore\Ext\Forms\Fields\Select`
 *    - `\MvcCore\Ext\Forms\Fields\CountrySelect`
 * - `\MvcCore\Ext\Forms\CheckboxGroup`
 * - `\MvcCore\Ext\Forms\Validators\MinMaxOptions`
 */
interface IMinMaxOptions {
	
	/**
	 * Get minimum options count to select. 
	 * Default value is `NULL` to not limit anything.
	 * @return int|NULL
	 */
	public function GetMinOptions ();
	
	/**
	 * Set minimum options count to select. 
	 * Default value is `NULL` to not limit anything.
	 * @param  int|NULL $minOptions
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetMinOptions ($minOptions);

	/**
	 * Get maximum options count to select. 
	 * Default value is `NULL` to not limit anything.
	 * @return int|NULL
	 */
	public function GetMaxOptions ();
	
	/**
	 * Set maximum options count to select. 
	 * Default value is `NULL` to not limit anything.
	 * @param  int|NULL $maxOptions
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetMaxOptions ($maxOptions);

	/**
	 * Get field is required bubble message for javascript.
	 * This method could be used only for checkbox group control.
	 * @return string
	 */
	public function GetRequiredBubbleMessage ();

	/**
	 * Set field is required bubble message for javascript.
	 * This method could be used only for checkbox group control.
	 * @param  string $requiredBubbleMessage 
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetRequiredBubbleMessage ($requiredBubbleMessage);

	/**
	 * Get minimum options bubble message for javascript.
	 * @return string
	 */
	public function GetMinOptionsBubbleMessage ();

	/**
	 * Set minimum options bubble message for javascript.
	 * @param  string $minOptionsBubbleMessage 
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetMinOptionsBubbleMessage ($minOptionsBubbleMessage);

	/**
	 * Get maximum options bubble message for javascript.
	 * @return string
	 */
	public function GetMaxOptionsBubbleMessage ();

	/**
	 * Set maximum options bubble message for javascript.
	 * @param  string $minOptionsBubbleMessage 
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetMaxOptionsBubbleMessage ($maxOptionsBubbleMessage);
}
