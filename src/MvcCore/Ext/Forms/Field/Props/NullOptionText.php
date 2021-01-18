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

namespace MvcCore\Ext\Forms\Field\Props;

/**
 * Trait for classes:
 * - `\MvcCore\Ext\Forms\Fields\Select`
 *    - `\MvcCore\Ext\Forms\Fields\CountrySelect`
 */
trait NullOptionText {

	/**
	 * This attribute is a text placeholder for `<select>` controls,
	 * when no option is selected yet. Typically: `--- please select an option ---`.
	 * It's rendered every time this placeholder has any text value, doesn't matter,
	 * if `<select>` tag has already any value selected or not yet . It's rendered usually 
	 * as first `<option>` sub-element with an empty value, as `disabled` and `selected` 
	 * `<option>` tag. `NULL` value means no placeholder `<option>` tag will be rendered.
	 * @var string|NULL
	 */
	protected $nullOptionText = NULL;
	
	/**
	 * Boolean to translate placeholder text, `TRUE` by default.
	 * @var boolean
	 */
	protected $translateNullOptionText = TRUE;

	/**
	 * Automatically translate `nullOptionText` property if necessary
	 * in `PreDispatch()` field rendering moment.
	 * @return void
	 */
	protected function preDispatchNullOptionText () {
		/** @var $this \MvcCore\Ext\Forms\Fields\Select */
		if ($this->translate && $this->nullOptionText !== NULL && $this->translateNullOptionText)
			$this->nullOptionText = $this->form->Translate($this->nullOptionText);
	}

	/**
	 * This attribute is a text placeholder for `<select>` controls,
	 * when no option is selected yet. Typically: `--- please select an option ---`.
	 * It's rendered every time this placeholder has any text value, doesn't matter,
	 * if `<select>` tag has already any value selected or not yet . It's rendered usually 
	 * as first `<option>` sub-element with an empty value, as `disabled` and `selected` 
	 * `<option>` tag. `NULL` value means no placeholder `<option>` tag will be rendered.
	 * @return string|NULL
	 */
	public function GetNullOptionText () {
		/** @var $this \MvcCore\Ext\Forms\Fields\Select */
		return $this->nullOptionText;
	}
	
	/**
	 * This attribute is a text placeholder for `<select>` controls,
	 * when no option is selected yet. Typically: `--- please select an option ---`.
	 * It's rendered every time this placeholder has any text value, doesn't matter,
	 * if `<select>` tag has already any value selected or not yet . It's rendered usually 
	 * as first `<option>` sub-element with an empty value, as `disabled` and `selected` 
	 * `<option>` tag. `NULL` value means no placeholder `<option>` tag will be rendered.
	 * @param string|NULL  $nullOptionText 
	 * @param boolean|NULL $translateNullOptionText 
	 * @return \MvcCore\Ext\Forms\Field
	 */
	public function SetNullOptionText ($nullOptionText, $translateNullOptionText = NULL) {
		/** @var $this \MvcCore\Ext\Forms\Fields\Select */
		$this->nullOptionText = $nullOptionText;
		if ($translateNullOptionText !== NULL)
			$this->translateNullOptionText = $translateNullOptionText;
		return $this;
	}

}
