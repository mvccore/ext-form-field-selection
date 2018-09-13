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

namespace MvcCore\Ext\Forms\Fields;

/**
 * Responsibility: init, predispatch and render `<input type="radio">` HTML 
 *				   element as radio buttons menu for single option selection.
 *				   `RadioGroup` field has it's own validator to check if 
 *				   submitted value is presented in configured options by default.
 */
class RadioGroup extends \MvcCore\Ext\Forms\FieldsGroup
{
	/**
	 * Possible values: `radio`.
	 * @var string
	 */
	protected $type = 'radio';

	/**
	 * Value type for `RadioGroup` is always `string` or `NULL`, not any array type.
	 * So it's necessary to change back the `value` property type
	 * defined in parent class `FieldsGroup` from `array` to `string|NULL`.
	 * @var string|NULL
	 */
	protected $value = NULL;

	/**
	 * Validators: 
	 * - `ValueInOptions` - to validate if submitted string is presented in radio options keys.
	 * @var string[]|\Closure[]
	 */
	protected $validators = ['ValueInOptions'];

	/**
	 * Radio group is always not marked as multiple value control. This function 
	 * always return `FALSE` for radio group instance.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-multiple
	 * @return bool
	 */
	public function GetMultiple () {
		return FALSE;
	}

	/**
	 * Field group is always not marked as multiple value control. This function 
	 * does nothing, because multiple option has to be `FALSE` for radio group instance all time.
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#attr-multiple
	 * @return \MvcCore\Ext\Forms\FieldsGroup|\MvcCore\Ext\Forms\IFieldsGroup
	 */
	public function & SetMultiple ($multiple = TRUE) {
		return $this;
	}

	/**
	 * Create new form `<input type="radio">` control instance.
	 * @param array $cfg Config array with public properties and it's 
	 *					 values which you want to configure, presented 
	 *					 in camel case properties names syntax.
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\Fields\RadioGroup|\MvcCore\Ext\Forms\IField
	 */
	public function __construct(array $cfg = []) {
		parent::__construct($cfg);
		static::$templates = (object) array_merge(
			(array) parent::$templates, 
			(array) self::$templates
		);
	}
}
