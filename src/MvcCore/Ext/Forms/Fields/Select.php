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
 * Responsibility: init, pre-dispatch and render `<select>` HTML element 
 *                 as roll-out menu for single option select or as options 
 *                 list for multiple selection. `Select` field has it's own 
 *                 validator to check if submitted value is presented in 
 *                 configured options by default.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class		Select 
extends		\MvcCore\Ext\Forms\Field 
implements	\MvcCore\Ext\Forms\Fields\IVisibleField, 
			\MvcCore\Ext\Forms\Fields\ILabel,
			\MvcCore\Ext\Forms\Fields\IMultiple, 
			\MvcCore\Ext\Forms\Fields\IOptions, 
			\MvcCore\Ext\Forms\Fields\IMinMaxOptions {

	use \MvcCore\Ext\Forms\Field\Props\VisibleField;
	use \MvcCore\Ext\Forms\Field\Props\Label;
	use \MvcCore\Ext\Forms\Field\Props\AutoComplete;
	use \MvcCore\Ext\Forms\Field\Props\Multiple;
	use \MvcCore\Ext\Forms\Field\Props\Options;
	use \MvcCore\Ext\Forms\Field\Props\MinMaxOptions;
	use \MvcCore\Ext\Forms\Field\Props\NullOptionText;
	use \MvcCore\Ext\Forms\Field\Props\Size;
	use \MvcCore\Ext\Forms\Field\Props\Wrapper;
	
	/**
	 * MvcCore Extension - Form - Field - Selection - version:
	 * Comparison by PHP function version_compare();
	 * @see http://php.net/manual/en/function.version-compare.php
	 */
	const VERSION = '5.1.9';

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
	 *                      are presented in select options keys.
	 * @var \string[]|\Closure[]
	 */
	protected $validators = ['ValueInOptions'];

	/**
	 * Standard field template strings for natural 
	 * rendering - `control`, `option` and `optionsGroup`.
	 * @var \string[]|\stdClass
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
		/** @var \MvcCore\Ext\Forms\Fields\Select $this */
		return $this->value;
	}
	
	/**
	 * If select has `multiple` boolean attribute, set to this 
	 * function `\string[]` array. If select has not `multiple` 
	 * attribute, set to this function `string`.
	 * If you don't want any selected value, set `NULL`.
	 * @param  array|string|NULL $value
	 * @return \MvcCore\Ext\Forms\Fields\Select
	 */
	public function SetValue ($value) {
		/** @var \MvcCore\Ext\Forms\Fields\Select $this */
		$this->value = $value;
		return $this;
	}
	
	/**
	 * Create new form `<select>` control instance.
	 * 
	 * @param  array                                            $cfg
	 * Config array with public properties and it's
	 * values which you want to configure, presented
	 * in camel case properties names syntax.
	 * 
	 * @param  string                                           $name 
	 * Form field specific name, used to identify submitted value.
	 * This value is required for all form fields.
	 * @param  string                                           $type 
	 * Fixed field order number, null by default.
	 * @param  int                                              $fieldOrder
	 * Form field type, used in `<input type="...">` attribute value.
	 * Every typed field has it's own string value, but base field type 
	 * `\MvcCore\Ext\Forms\Field` has `NULL`.
	 * @param  \float[]|\int[]|\string[]|float|int|string|array $value 
	 * Form field value. It could be string or array, int or float, it depends
	 * on field implementation. Default value is `NULL`.
	 * @param  string                                           $title 
	 * Field title, global HTML attribute, optional.
	 * @param  string                                           $translate 
	 * Boolean flag about field visible texts and error messages translation.
	 * This flag is automatically assigned from `$field->form->GetTranslate();` 
	 * flag in `$field->Init();` method.
	 * @param  string                                           $translateTitle 
	 * Boolean to translate title text, `TRUE` by default.
	 * @param  array                                            $cssClasses 
	 * Form field HTML element css classes strings.
	 * Default value is an empty array to not render HTML `class` attribute.
	 * @param  array                                            $controlAttrs 
	 * Collection with field HTML element additional attributes by array keys/values.
	 * Do not use system attributes as: `id`, `name`, `value`, `readonly`, `disabled`, `class` ...
	 * Those attributes has it's own configurable properties by setter methods or by constructor config array.
	 * HTML field elements are meant: `<input>, <button>, <select>, <textarea> ...`. 
	 * Default value is an empty array to not render any additional attributes.
	 * @param  array                                            $validators 
	 * List of predefined validator classes ending names or validator instances.
	 * Keys are validators ending names and values are validators ending names or instances.
	 * Validator class must exist in any validators namespace(s) configured by default:
	 * - `array('\MvcCore\Ext\Forms\Validators\');`
	 * Or it could exist in any other validators namespaces, configured by method(s):
	 * - `\MvcCore\Ext\Form::AddValidatorsNamespaces(...);`
	 * - `\MvcCore\Ext\Form::SetValidatorsNamespaces(...);`
	 * Every given validator class (ending name) or given validator instance has to 
	 * implement interface  `\MvcCore\Ext\Forms\IValidator` or it could be extended 
	 * from base  abstract validator class: `\MvcCore\Ext\Forms\Validator`.
	 * Every typed field has it's own predefined validators, but you can define any
	 * validator you want and replace them.
	 * 
	 * @param  string                                           $accessKey
	 * The access key global attribute provides a hint for generating
	 * a keyboard shortcut for the current element. The attribute 
	 * value must consist of a single printable character (which 
	 * includes accented and other characters that can be generated 
	 * by the keyboard).
	 * @param  bool                                             $autoFocus
	 * This Boolean attribute lets you specify that a form control should have input
	 * focus when the page loads. Only one form-associated element in a document can
	 * have this attribute specified. 
	 * @param  bool                                             $disabled
	 * Form field attribute `disabled`, determination if field value will be 
	 * possible to change by user and if user will be graphically informed about it 
	 * by default browser behaviour or not. Default value is `FALSE`. 
	 * This flag is also used for sure for submit checking. But if any field is 
	 * marked as disabled, browsers always don't send any value under this field name
	 * in submit. If field is configured as disabled, no value sent under field name 
	 * from user will be accepted in submit process and value for this field will 
	 * be used by server side form initialization. 
	 * Disabled attribute has more power than required. If disabled is true and
	 * required is true and if there is no or invalid submitted value, there is no 
	 * required error and it's used value from server side assigned by 
	 * `$form->SetValues();` or from session.
	 * @param  bool                                             $readOnly
	 * Form field attribute `readonly`, determination if field value will be 
	 * possible to read only or if value will be possible to change by user. 
	 * Default value is `FALSE`. This flag is also used for submit checking. 
	 * If any field is marked as read only, browsers always send value in submit.
	 * If field is configured as read only, no value sent under field name 
	 * from user will be accepted in submit process and value for this field 
	 * will be used by server side form initialization. 
	 * Readonly attribute has more power than required. If readonly is true and
	 * required is true and if there is invalid submitted value, there is no required 
	 * error and it's used value from server side assigned by 
	 * `$form->SetValues();` or from session.
	 * @param  bool                                             $required
	 * Form field attribute `required`, determination
	 * if control will be required to complete any value by user.
	 * This flag is also used for submit checking. Default value is `NULL`
	 * to not require any field value. If form has configured it's property
	 * `$form->GetDefaultRequired()` to `TRUE` and this value is `NULL`, field
	 * will be automatically required by default form configuration.
	 * @param  int|string                                       $tabIndex
	 * An integer attribute indicating if the element can take input focus (is focusable), 
	 * if it should participate to sequential keyboard navigation, and if so, at what 
	 * position. You can set `auto` string value to get next form tab-index value automatically. 
	 * Tab-index for every field in form is better to index from value `1` or automatically and 
	 * moved to specific higher value by place, where is form currently rendered by form 
	 * instance method `$form->SetBaseTabIndex()` to move tab-index for each field into 
	 * final values. Tab-index can takes several values:
	 * - a negative value means that the element should be focusable, but should not be 
	 *   reachable via sequential keyboard navigation;
	 * - 0 means that the element should be focusable and reachable via sequential 
	 *   keyboard navigation, but its relative order is defined by the platform convention;
	 * - a positive value means that the element should be focusable and reachable via 
	 *   sequential keyboard navigation; the order in which the elements are focused is 
	 *   the increasing value of the tab-index. If several elements share the same tab-index, 
	 *   their relative order follows their relative positions in the document.
	 * 
	 * @param  string                                           $label
	 * Control label visible text. If field form has configured any translator, translation 
	 * will be processed automatically before rendering process. Default value is `NULL`.
	 * @param  bool                                             $translateLabel
	 * Boolean to translate label text, `TRUE` by default.
	 * @param  string                                           $labelSide
	 * Label side from rendered field - location where label will be rendered.
	 * By default `$this->labelSide` is configured to `left`.
	 * If you want to reconfigure it to different side,
	 * the only possible value is `right`.
	 * You can use constants:
	 * - `\MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT`
	 * - `\MvcCore\Ext\Forms\IField::LABEL_SIDE_RIGHT`
	 * @param  int                                              $renderMode
	 * Rendering mode flag how to render field and it's label.
	 * Default value is `normal` to render label and field, label 
	 * first or field first by another property `$field->labelSide = 'left' | 'right';`.
	 * But if you want to render label around field or if you don't want
	 * to render any label, you can change this with constants (values):
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_NORMAL`       - `<label /><input />`
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_LABEL_AROUND` - `<label><input /></label>`
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_NO_LABEL`     - `<input />`
	 * @param  array                                            $labelAttrs
	 * Collection with `<label>` HTML element additional attributes by array keys/values.
	 * Do not use system attributes as: `id`,`for` or `class`, those attributes has it's own 
	 * configurable properties by setter methods or by constructor config array. Label `class` 
	 * attribute has always the same css classes as it's field automatically. 
	 * Default value is an empty array to not render any additional attributes.
	 * 
	 * @param  string                                           $autoComplete 
	 * Attribute indicates if the input can be automatically completed 
	 * by the browser, usually by remembering previous values the user 
	 * has entered. Possible values: `off`, `on`, `name`, `email`, 
	 * `username`, `country`, `postal-code` and many more...
	 * 
	 * @param  bool                                             $multiple
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
	 * 
	 * @param  array                                            $options
	 * Form group control options to render more sub-control attributes for specified
	 * submitted values (array keys). This property configuration is required.
	 * @param  bool                                             $translateOptions
	 * Boolean about to translate options texts, default `TRUE` to translate.
	 * @param  array                                            $optionsLoader
	 * Definition for method name and context to resolve options loading for complex cases.
	 * First item is string method name, which has to return options for `$field->SetOptions()` method.
	 * Second item is context definition int flag, where the method is located, you can use constants:
	 *  - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_FORM`
	 *  - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_FORM_STATIC`
	 *  - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_CTRL`
	 *  - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_CTRL_STATIC`
	 *  - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_MODEL`
	 *  - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_MODEL_STATIC`
	 * Last two constants are usefull only for `mvccore/ext-model-form` extension.
	 * 
	 * @param  int                                              $minOptions
	 * Minimum options count to select. 
	 * Default value is `NULL` to not limit anything.
	 * @param  int                                              $maxOptions
	 * Maximum options count to select. 
	 * Default value is `NULL` to not limit anything.
	 * 
	 * @param  string                                           $nullOptionText 
	 * This attribute is a text placeholder for `<select>` controls,
	 * when no option is selected yet. Typically: `--- please select an option ---`.
	 * It's rendered every time this placeholder has any text value, doesn't matter,
	 * if `<select>` tag has already any value selected or not yet . It's rendered usually 
	 * as first `<option>` sub-element with an empty value, as `disabled` and `selected` 
	 * `<option>` tag. `NULL` value means no placeholder `<option>` tag will be rendered.
	 * @param  bool                                             $translateNullOptionText
	 * Boolean to translate placeholder text, `TRUE` by default.
	 * 
	 * @param  int                                              $size
	 * If the field is `<input>`, this attribute is initial size of the control. Starting in HTML5, 
	 * this attribute applies only when the `type` attribute is set to `text`, `search`, `tel`, `url`, 
	 * `email`, or `password`, otherwise it is ignored. The `size` must be an integer greater than zero. 
	 * The default browser`s value is 20.
	 * If the field is `<select>`, this attribute is presented as a scrolling list box (e.g. when 
	 * `multiple` attribute is specified to `TRUE`), this attribute represents the number of rows in 
	 * the list that should be visible at one time. Browsers are not required to present a select element 
	 * as a scrolled list box. The default browser`s value is `0`.
	 * 
	 * @param  string                                           $wrapper
	 * Html code wrapper, wrapper has to contain replacement in string 
	 * form: `{control}`. Around this substring you can wrap any HTML 
	 * code you want. Default wrapper values is: `'{control}'`.
	 * 
	 * @throws \InvalidArgumentException
	 * @return void
	 */
	public function __construct(
		array $cfg = [],

		$name = NULL,
		$type = NULL,
		$fieldOrder = NULL,
		$value = NULL,
		$title = NULL,
		$translate = NULL,
		$translateTitle = NULL,
		array $cssClasses = [],
		array $controlAttrs = [],
		array $validators = [],
		
		$accessKey = NULL,
		$autoFocus = NULL,
		$disabled = NULL,
		$readOnly = NULL,
		$required = NULL,
		$tabIndex = NULL,

		$label = NULL,
		$translateLabel = TRUE,
		$labelSide = NULL,
		$renderMode = NULL,
		array $labelAttrs = [],

		$autoComplete = NULL,
		$multiple = NULL,
		array $options = [],
		$translateOptions = TRUE,
		array $optionsLoader = [],
		$minOptions = NULL,
		$maxOptions = NULL,
		$nullOptionText = NULL,
		$translateNullOptionText = TRUE,
		$size = NULL,
		$wrapper = NULL
	) {
		$this->consolidateCfg($cfg, func_get_args(), func_num_args());
		parent::__construct($cfg);
		static::$templates = (object) array_merge(
			(array) parent::$templates, 
			(array) self::$templates
		);
		$this->ctorOptions($cfg);
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
	 * @param  \MvcCore\Ext\Form $form
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\Fields\Select
	 */
	public function SetForm (\MvcCore\Ext\IForm $form) {
		/** @var \MvcCore\Ext\Forms\Fields\Select $this */
		if ($this->form !== NULL) return $this;
		parent::SetForm($form);
		$this->setFormLoadOptions();
		if (!$this->options && !$this->optionsLoader) 
			$this->throwNewInvalidArgumentException(
				'No `options` property or `optionsLoader` defined.'
			);
		// add minimum/maximum options count validator if necessary
		$this->setFormMinMaxOptions();
		return $this;
	}

	/**
	 * Return field specific data for validator.
	 * @param  array $fieldPropsDefaultValidValues 
	 * @return array
	 */
	public function & GetValidatorData ($fieldPropsDefaultValidValues = []) {
		/** @var \MvcCore\Ext\Forms\Fields\Select $this */
		$result = [
			'multiple'		=> $this->multiple, 
			'options'		=> & $this->options, 
			'minOptions'	=> $this->minOptions,
			'maxOptions'	=> $this->maxOptions,
		];
		return $result;
	}

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Form` just before
	 * field is naturally rendered. It sets up field for rendering process.
	 * Do not use this method even if you don't develop any form field.
	 * Set up field properties before rendering process.
	 * - Set up field render mode if not defined.
	 * - Translate label text if necessary.
	 * - Set up tab-index if necessary.
	 * - Translate all options if necessary, including null option text if necessary.
	 * @return void
	 */
	public function PreDispatch () {
		/** @var \MvcCore\Ext\Forms\Fields\Select $this */
		parent::PreDispatch();
		$this->preDispatchTabIndex();
		if (!$this->translate) return;
		$this->preDispatchNullOptionText();
		if (!$this->translateOptions) return;
		$form = $this->form;
		foreach ($this->options as $key => & $value) {
			if (is_scalar($value)) { // string|int|float|bool
				// most simple key/value array options configuration
				if ($value) 
					$options[$key] = $form->Translate((string) $value);
			} else if (is_array($value)) {
				if (isset($value['options']) && is_array($value['options'])) {
					// `<optgroup>` options configuration
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
		/** @var \MvcCore\Ext\Forms\Fields\Select $this */
		$form = $this->form;
		$groupLabel = isset($optionsGroup['label']) 
			? $optionsGroup['label'] 
			: '';
		if ($groupLabel)
			$optionsGroup['label'] = $form->Translate((string) $groupLabel);
		$groupOptions = $optionsGroup['options'] 
			? $optionsGroup['options'] 
			: [];
		foreach ($groupOptions as $key => & $groupOption) {
			if (is_scalar($groupOption)) {
				// most simple key/value array options configuration
				if ($groupOption) 
					$optionsGroup['options'][$key] = $form->Translate((string) $groupOption);
			} else if (is_array($groupOption)) {
				// advanced configuration with key, text, CSS class, and any other attributes for single option tag
				$valueText = isset($groupOption['text']) ? $groupOption['text'] : $key;
				if ($valueText) $groupOption['text'] = $form->Translate((string) $valueText);
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
		/** @var \MvcCore\Ext\Forms\Fields\Select $this */
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
		/** @var \stdClass $templates */
		$templates = static::$templates;
		$result = $formViewClass::Format($templates->control, [
			'id'		=> $this->id,
			'name'		=> $name,
			'size'		=> $size,
			'options'	=> $optionsStr,
			'attrs'		=> strlen($attrsStr) > 0 ? ' ' . $attrsStr : '',
		]);
		return $this->renderControlWrapper($result);
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
		/** @var \MvcCore\Ext\Forms\Fields\Select $this */
		$result = '';
		$valueTypeIsArray = is_array($this->value);
		if ($this->nullOptionText !== NULL && mb_strlen((string) $this->nullOptionText) > 0) {
			// advanced configuration with key, text, css class, and any other attributes for single option tag
			$result .= $this->renderControlOptionsAdvanced(
				NULL, [
					'value'	=> NULL,
					'text'	=> htmlspecialchars_decode(htmlspecialchars($this->nullOptionText, ENT_QUOTES), ENT_QUOTES),
					//'attrs'	=> ['disabled' => 'disabled'] // this will cause the browser to select the first allowed option automatically 
				], $valueTypeIsArray
			);
		}
		foreach ($this->options as $key => & $value) {
			if (is_scalar($value)) {
				// most simple key/value array options configuration
				$result .= $this->renderControlOptionKeyValue($key, $value, $valueTypeIsArray);
			} else if (is_array($value)) {
				if (isset($value['options']) && is_array($value['options'])) {
					// `<optgroup>` options configuration
					$result .= $this->renderControlOptionsGroup($value, $valueTypeIsArray);
				} else {
					// advanced configuration with key, text, cs class, and any other attributes for single option tag
					$result .= $this->renderControlOptionsAdvanced(
						isset($value['value']) 
							? $value['value'] 
							: $key, 
						$value, 
						$valueTypeIsArray
					);
				}
			}
		}
		return $result;
	}
	
	/**
	 * Render select `<option>` tag with inner visible text and attributes: `value` and
	 * `selected` (optionally) by given `$value` string for value to select and `$text` 
	 * string for visible text.
	 * @param  string|NULL $value 
	 * @param  string      $text 
	 * @param  bool        $valueTypeIsArray 
	 * @return string
	 */
	protected function renderControlOptionKeyValue ($value, & $text, $valueTypeIsArray) {
		/** @var \MvcCore\Ext\Forms\Fields\Select $this */
		$selected = $valueTypeIsArray
			? in_array($value, $this->value, TRUE)
			: $this->value === $value ;
		$formViewClass = $this->form->GetViewClass();
		/** @var \stdClass $templates */
		$templates = static::$templates;
		return $formViewClass::Format($templates->option, [
			'value'		=> htmlspecialchars_decode(htmlspecialchars($value, ENT_QUOTES), ENT_QUOTES),
			'selected'	=> $selected ? ' selected="selected"' : '',
			'text'		=> htmlspecialchars_decode(htmlspecialchars($text, ENT_QUOTES), ENT_QUOTES),
			'class'		=> '', // to fill prepared template control place for attribute class with empty string
			'attrs'		=> '', // to fill prepared template control place for other attributes with empty string
		]);
	}

	/**
	 * Render `<optgroup>` tag including it's rendered `<option>` tags.
	 * @param  array $optionsGroup 
	 * @param  bool  $valueTypeIsArray 
	 * @return string
	 */
	protected function renderControlOptionsGroup (& $optionsGroup, $valueTypeIsArray) {
		/** @var \MvcCore\Ext\Forms\Fields\Select $this */
		$optionsStr = '';
		foreach ($optionsGroup['options'] as $key => & $value) {
			if (is_scalar($value)) {
				// most simple key/value array options configuration
				$optionsStr .= $this->renderControlOptionKeyValue($key, $value, $valueTypeIsArray);
			} else if (is_array($value)) {
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
		/** @var \stdClass $templates */
		$templates = static::$templates;
		return $formViewClass::Format($templates->optionsGroup, [
			'options'	=> $optionsStr,
			'label'		=> ' label="' . $label. '"',
			'class'		=> $classStr,
			'attrs'		=> $attrsStr
		]);
	}

	/**
	 * Render select `<option>` tag with inner visible text and attributes: `value`, 
	 * `selected` (optionally), `class` (optionally) and any other optional attributes if configured
	 * by given `$value` string and `$optionData` array with additional option configuration data.
	 * @param  string|NULL $value 
	 * @param  mixed       $optionData 
	 * @param  mixed       $valueTypeIsArray 
	 * @return mixed
	 */
	protected function renderControlOptionsAdvanced ($value, $optionData, $valueTypeIsArray) {
		/** @var \MvcCore\Ext\Forms\Fields\Select $this */
		$valueToRender = isset($optionData['value']) 
			? $optionData['value'] 
			: ($value === NULL ? '' : $value);
		if ($valueTypeIsArray) {
			if (count($this->value) > 0) {
				$selected = in_array($valueToRender, $this->value, TRUE);
			} else {
				$selected = $valueToRender === NULL;
			}
		} else {
			$selected = $this->value === $valueToRender;
		}
		$formViewClass = $this->form->GetViewClass();
		$classStr = isset($optionData['class']) && strlen((string) $optionData['class'])
			? ' class="' . $optionData['class'] . '"'
			: '';
		$attrsStr = isset($optionData['attrs']) 
			? ' ' . $formViewClass::RenderAttrs($optionData['attrs']) 
			: '';
		/** @var \stdClass $templates */
		$templates = static::$templates;
		return $formViewClass::Format($templates->option, [
			'value'		=> htmlspecialchars_decode(htmlspecialchars($valueToRender, ENT_QUOTES), ENT_QUOTES),
			'selected'	=> $selected ? ' selected="selected"' : '',
			'class'		=> $classStr,
			'attrs'		=> $attrsStr,
			'text'		=> htmlspecialchars_decode(htmlspecialchars($optionData['text'], ENT_QUOTES), ENT_QUOTES),
		]);
	}
}
