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
 * Responsibility: init, predispatch and render `<select>` HTML element 
 *				   as rollout menu for single option select or as options 
 *				   list for multiple selection. `Select` field has it's own 
 *				   validator to check if submitted value is presented in 
 *				   configured options by default.
 */
class Select 
	extends		\MvcCore\Ext\Forms\Field 
	implements	\MvcCore\Ext\Forms\Fields\IVisibleField, 
				\MvcCore\Ext\Forms\Fields\ILabel,
				\MvcCore\Ext\Forms\Fields\IMultiple, 
				\MvcCore\Ext\Forms\Fields\IOptions, 
				\MvcCore\Ext\Forms\Fields\IMinMaxOptions
{
	use \MvcCore\Ext\Forms\Field\Props\VisibleField;
	use \MvcCore\Ext\Forms\Field\Props\Label;
	use \MvcCore\Ext\Forms\Field\Props\AutoComplete;
	use \MvcCore\Ext\Forms\Field\Props\Multiple;
	use \MvcCore\Ext\Forms\Field\Props\Options;
	use \MvcCore\Ext\Forms\Field\Props\MinMaxOptions;
	use \MvcCore\Ext\Forms\Field\Props\NullOptionText;
	use \MvcCore\Ext\Forms\Field\Props\Size;

	/**
	 * Possible value: `select`, not used in HTML code for this field.
	 * @var string
	 */
	protected $type = 'select';

	/**
	 * Possible values: a string value, array of strings (for select with `multiple` attribute) or `NULL`.
	 * @var string|array|NULL
	 */
	protected $value = NULL;
	
	/**
	 * Validators: 
	 * - `ValueInOptions` - to validate if submitted string(s) 
	 *						are presented in select options keys.
	 * @var string[]|\Closure[]
	 */
	protected $validators = ['ValueInOptions'];

	/**
	 * Standard field template strings for natural 
	 * rendering - `control`, `option` and `optionsGroup`.
	 * @var string
	 */
	protected static $templates = [
		'control'		=> '<select id="{id}" name="{name}"{size}{attrs}>{options}</select>',
		'option'		=> '<option value="{value}"{selected}{class}{attrs}>{text}</option>',
		'optionsGroup'	=> '<optgroup{label}{class}{attrs}>{options}</optgroup>',
	];

	/**
	 * If select has `multiple` boolean attribute defined, this 
	 * function returns `\string[]` array. If select has no `multiple`
	 * attribute, this function returns `string`.
	 * If there is no value selected or configured, function returns `NULL`.
	 * @return array|string|NULL
	 */
	public function GetValue () {
		return $this->value;
	}
	
	/**
	 * If select has `multiple` boolean attribute, set to this 
	 * function `\string[]` array. If select has not `multiple` 
	 * attribute, set to this function `string`.
	 * If you don't want any selected value, set `NULL`.
	 * @param array|string|NULL $value
	 * @return \MvcCore\Ext\Forms\Fields\Select|\MvcCore\Ext\Forms\IField
	 */
	public function & SetValue ($value) {
		$this->value = $value;
		return $this;
	}
	
	/**
	 * Create new form `<select>` control instance.
	 * @param array $cfg Config array with public properties and it's 
	 *					 values which you want to configure, presented 
	 *					 in camel case properties names syntax.
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\Fields\Select|\MvcCore\Ext\Forms\IField
	 */
	public function __construct(array $cfg = []) {
		parent::__construct($cfg);
		static::$templates = (object) array_merge(
			(array) parent::$templates, 
			(array) self::$templates
		);
	}

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Form` after field
	 * is added into form instance by `$form->AddField();` method. Do not 
	 * use this method even if you don't develop any form field.
	 * - Check if field has any name, which is required.
	 * - Set up form and field id attribute by form id and field name.
	 * - Set up required.
	 * - Set up translate boolean property.
	 * - Check if there are any select options in `$this->options`.
	 * - Set up select minimum/maximum options to select if necessary.
	 * @param \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm $form
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\Fields\Select|\MvcCore\Ext\Forms\IField
	 */
	public function & SetForm (\MvcCore\Ext\Forms\IForm & $form) {
		parent::SetForm($form);
		if (!$this->options) $this->throwNewInvalidArgumentException(
			'No `options` property defined.'
		);
		// add minimum/maximum options count validator if necessary
		$this->setFormMinMaxOptions();
	}

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Form` just before
	 * field is naturally rendered. It sets up field for rendering process.
	 * Do not use this method even if you don't develop any form field.
	 * Set up field properties before rendering process.
	 * - Set up field render mode if not defined.
	 * - Translate label text if necessary.
	 * - Set up tabindex if necessary.
	 * - Translate all options if necessary, including null option text if necessary.
	 * @return void
	 */
	public function PreDispatch () {
		parent::PreDispatch();
		$this->preDispatchTabIndex();
		if (!$this->translate) return;
		$form = & $this->form;
		if ($this->nullOptionText !== NULL && $this->nullOptionText !== '')
			$this->nullOptionText = $form->translate($this->nullOptionText);
		foreach ($this->options as $key => & $value) {
			$valueType = gettype($value);
			if ($valueType == 'string') {
				// most simple key/value array options configuration
				if ($value) 
					$options[$key] = $form->Translate((string)$value);
			} else if ($valueType == 'array') {
				if (isset($value['options']) && gettype($value['options']) == 'array') {
					// optgroup options configuration
					$this->preDispatchTranslateOptionOptGroup($value);
				} else {
					// advanced configuration with key, text, css class, and any other attributes for single option tag
					$valueText = isset($value['text']) ? $value['text'] : $key;
					if ($valueText) $value['text'] = $form->Translate((string) $valueText);
				}
			}
		}
	}

	/**
	 * Translate select option item if option item is configured as array for option group.
	 * @param array & $optionsGroup 
	 */
	protected function preDispatchTranslateOptionOptGroup (& $optionsGroup) {
		$form = & $this->form;
		$groupLabel = isset($optionsGroup['label']) 
			? $optionsGroup['label'] 
			: '';
		if ($groupLabel)
			$optionsGroup['label'] = $form->Translate((string) $groupLabel);
		$groupOptions = $optionsGroup['options'] 
			? $optionsGroup['options'] 
			: [];
		foreach ($groupOptions as $key => & $groupOption) {
			$groupOptionType = gettype($groupOption);
			if ($groupOptionType == 'string') {
				// most simple key/value array options configuration
				if ($groupOption) 
					$optionsGroup['options'][$key] = $form->Translate((string) $groupOption);
			} else if ($groupOptionType == 'array') {
				// advanced configuration with key, text, cs class, and any other attributes for single option tag
				$valueText = isset($groupOption['text']) ? $groupOption['text'] : $key;
				if ($valueText) $groupOption['text'] = $this->form->Translate((string) $valueText);
			}
		}
	}

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\Field\Rendering` 
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 * 
	 * Render control tag only without label or specific errors,
	 * including all select `<option>` tags or `<optgroup>` tags
	 * if there are options configured for.
	 * @return string
	 */
	public function RenderControl () {
		$optionsStr = $this->RenderControlOptions();
		$attrsStr = $this->renderControlAttrsWithFieldVars([
			'autoComplete',
		]);
		if ($this->multiple) {
			$attrsStr .= (strlen($attrsStr) > 0 ? ' ' : '')
				. 'multiple="multiple"';
			$name = $this->name . '[]';
			$size = $this->size !== NULL ? ' size="' . $this->size . '"' : '';
		} else {
			$name = $this->name;
			$size = '';
		}
		if (!$this->form->GetFormTagRenderingStatus()) 
			$attrsStr .= (strlen($attrsStr) > 0 ? ' ' : '')
				. 'form="' . $this->form->GetId() . '"';
		$formViewClass = $this->form->GetViewClass();
		return $formViewClass::Format(static::$templates->control, [
			'id'		=> $this->id,
			'name'		=> $name,
			'size'		=> $size,
			'options'	=> $optionsStr,
			'attrs'		=> strlen($attrsStr) > 0 ? ' ' . $attrsStr : '',
		]);
	}

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\Field\Rendering` 
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 * 
	 * Render inner select control `<option>` tags or `<optgroup>` 
	 * tags if there are options configured for.
	 * @return string
	 */
	public function RenderControlOptions () {
		$result = '';
		$valueTypeIsArray = gettype($this->value) == 'array';
		if ($this->nullOptionText !== NULL && mb_strlen((string) $this->nullOptionText) > 0) {
			// advanced configuration with key, text, css class, and any other attributes for single option tag
			$result .= $this->renderControlOptionsAdvanced(
				NULL, [
					'value'	=> '',
					'text'	=> htmlspecialchars($this->nullOptionText, ENT_QUOTES),
					'attrs'	=> ['disabled' => 'disabled']
				], $valueTypeIsArray
			);
		}
		foreach ($this->options as $key => & $value) {
			$valueType = gettype($value);
			if ($valueType == 'string') {
				// most simple key/value array options configuration
				$result .= $this->renderControlOptionKeyValue($key, $value, $valueTypeIsArray);
			} else if ($valueType == 'array') {
				if (isset($value['options']) && gettype($value['options']) == 'array') {
					// optgroup options configuration
					$result .= $this->renderControlOptionsGroup($value, $valueTypeIsArray);
				} else {
					// advanced configuration with key, text, cs class, and any other attributes for single option tag
					$result .= $this->renderControlOptionsAdvanced($key, $value, $valueTypeIsArray);
				}
			}
		}
		return $result;
	}
	
	/**
	 * Render select `<option>` tag with inner visible text and attributes: `value` and
	 * `selected` (optionaly) by given `$value` string for value to select and `$text` 
	 * string for visible text.
	 * @param string|NULL $value 
	 * @param string $text 
	 * @param bool $valueTypeIsArray 
	 * @return string
	 */
	protected function renderControlOptionKeyValue ($value, & $text, $valueTypeIsArray) {
		$selected = $valueTypeIsArray
			? in_array($value, $this->value)
			: $this->value === $value ;
		$formViewClass = $this->form->GetViewClass();
		return $formViewClass::Format(static::$templates->option, [
			'value'		=> htmlspecialchars($value, ENT_QUOTES),
			'selected'	=> $selected ? ' selected="selected"' : '',
			'text'		=> htmlspecialchars($text, ENT_QUOTES),
			'class'		=> '', // to fill prepared template control place for attribute class with empty string
			'attrs'		=> '', // to fill prepared template control place for other attributes with empty string
		]);
	}

	/**
	 * Render `<optgroup>` tag including it's rendered `<option>` tags.
	 * @param array $optionsGroup 
	 * @param bool $valueTypeIsArray 
	 * @return string
	 */
	protected function renderControlOptionsGroup (& $optionsGroup, $valueTypeIsArray) {
		$optionsStr = '';
		foreach ($optionsGroup['options'] as $key => & $value) {
			$valueType = gettype($value);
			if ($valueType == 'string') {
				// most simple key/value array options configuration
				$optionsStr .= $this->renderControlOptionKeyValue($key, $value, $valueTypeIsArray);
			} else if ($valueType == 'array') {
				// advanced configuration with key, text, cs class, and any other attributes for single option tag
				$optionsStr .= $this->renderControlOptionsAdvanced($key, $value, $valueTypeIsArray);
			}
		}
		$label = isset($optionsGroup['label']) && strlen((string) $optionsGroup['label']) > 0
			? $optionsGroup['label']
			: NULL;
		if (!$optionsStr && !$label) return '';
		$formViewClass = $this->form->GetViewClass();
		$classStr = isset($optionsGroup['class']) && strlen((string) $optionsGroup['class'])
			? ' class="' . $optionsGroup['class'] . '"'
			: '';
		$attrsStr = isset($optionsGroup['attrs']) 
			? ' ' . $formViewClass::RenderAttrs($optionsGroup['attrs']) 
			: '';
		return $formViewClass::Format(static::$templates->optionsGroup, [
			'options'	=> $optionsStr,
			'label'		=> ' label="' . $label. '"',
			'class'		=> $classStr,
			'attrs'		=> $attrsStr
		]);
	}

	/**
	 * Render select `<option>` tag with inner visible text and attributes: `value`, 
	 * `selected` (optionaly), `class` (optionaly) and any other optional attributes if configured
	 * by given `$value` string and `$optionData` array with additional option configuration data.
	 * @param string|NULL $value 
	 * @param mixed $optionData 
	 * @param mixed $valueTypeIsArray 
	 * @return mixed
	 */
	protected function renderControlOptionsAdvanced ($value, $optionData, $valueTypeIsArray) {
		$valueToRender = isset($optionData['value']) 
			? $optionData['value'] 
			: ($value === NULL ? '' : $value);
		if ($valueTypeIsArray) {
			if (count($this->value) > 0) {
				$selected = in_array($value, $this->value);
			} else {
				$selected = $value === NULL;
			}
		} else {
			$selected = $this->value === $value;
		}
		$formViewClass = $this->form->GetViewClass();
		$classStr = isset($optionData['class']) && strlen((string) $optionData['class'])
			? ' class="' . $optionData['class'] . '"'
			: '';
		$attrsStr = isset($optionData['attrs']) 
			? ' ' . $formViewClass::RenderAttrs($optionData['attrs']) 
			: '';
		return $formViewClass::Format(static::$templates->option, [
			'value'		=> htmlspecialchars($valueToRender, ENT_QUOTES),
			'selected'	=> $selected ? ' selected="selected"' : '',
			'class'		=> $classStr,
			'attrs'		=> $attrsStr,
			'text'		=> htmlspecialchars($optionData['text'], ENT_QUOTES),
		]);
	}
}
