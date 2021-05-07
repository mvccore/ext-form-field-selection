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
 * Responsibility: init, pre-dispatch and render group of `input`s 
 *                 with `type` as `checkbox`, with configuration to 
 *                 select minimum and maximum count of values and 
 *                 required option. 
 *                 `CheckboxGroup` has it's own validator to check if 
 *                 submitted values are presented in configured by 
 *                 default and it's own validator to check minimum or 
 *                 maximum count of selected options.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class		CheckboxGroup 
extends		\MvcCore\Ext\Forms\FieldsGroup 
implements	\MvcCore\Ext\Forms\Fields\IMinMaxOptions {

	use \MvcCore\Ext\Forms\Field\Props\MinMaxOptions;
	
	/**
	 * Valid email address error message index.
	 * @var int
	 */
	const ERROR_REQUIRED_BUBBLE = 0;
	const ERROR_MIN_OPTIONS_BUBBLE = 1;
	const ERROR_MAX_OPTIONS_BUBBLE = 2;

	/**
	 * Validation failure message template definitions.
	 * @var array
	 */
	protected static $errorMessages = [
		self::ERROR_REQUIRED_BUBBLE		=> "Please tick this box, field is required.",
		self::ERROR_MIN_OPTIONS_BUBBLE	=> "Please select at least `{1}` option(s) as minimum.",
		self::ERROR_MAX_OPTIONS_BUBBLE	=> "Please select up to `{1}` option(s) at maximum.",
	];

	/**
	 * Possible value: `checkbox`, used in HTML code for this fields.
	 * @var string
	 */
	protected $type = 'checkbox-group';
	
	/**
	 * Validators: 
	 * - `ValueInOptions` - to validate if submitted string(s) 
	 *                      are presented in select options keys.
	 * @var \string[]|\Closure[]
	 */
	protected $validators = ["ValueInOptions"];

	/**
	 * Supporting javascript full javascript class name.
	 * If you want to use any custom supporting javascript prototyped class
	 * for any additional purposes for your custom field, you need to use
	 * `$field->jsSupportingFile` property to define path to your javascript file
	 * relatively from configured `\MvcCore\Ext\Form::SetJsSupportFilesRootDir(...);`
	 * value. Than you have to add supporting javascript file path into field form 
	 * in `$field->PreDispatch();` method to render those files immediately after form
	 * (once) or by any external custom assets renderer configured by:
	 * `$form->SetJsSupportFilesRenderer(...);` method.
	 * Or you can add your custom supporting javascript files into response by your 
	 * own and also you can run your helper javascripts also by your own. Is up to you.
	 * `NULL` by default.
	 * @var string
	 */
	protected $jsClassName = 'MvcCoreForm.CheckboxGroup';

	/**
	 * Field supporting javascript file relative path.
	 * If you want to use any custom supporting javascript file (with prototyped 
	 * class) for any additional purposes for your custom field, you need to 
	 * define path to your javascript file relatively from configured 
	 * `\MvcCore\Ext\Form::SetJsSupportFilesRootDir(...);` value. 
	 * Than you have to add supporting javascript file path into field form 
	 * in `$field->PreDispatch();` method to render those files immediately after form
	 * (once) or by any external custom assets renderer configured by:
	 * `$form->SetJsSupportFilesRenderer(...);` method.
	 * Or you can add your custom supporting javascript files into response by your 
	 * own and also you can run your helper javascripts also by your own. Is up to you.
	 * `NULL` by default.
	 * @var string
	 */
	protected $jsSupportingFile = \MvcCore\Ext\IForm::FORM_ASSETS_DIR_REPLACEMENT . '/fields/checkbox-group.js';

	/**
	 * Maximum options specific css class for supporting javascript code.
	 * @var string
	 */
	protected $maxOptionsClassName = 'max-selected-options';

	/**
	 * Standard field template strings for natural rendering a `control`.
	 * @var \string[]|\stdClass
	 */
	protected static $templates = [
		'control'	=> '<input id="{id}" name="{name}[]" type="checkbox" value="{value}"{checked}{attrs} />',
	];

	/**
	 * Create new form `<input type="checkbox" />` group control instance.
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
	 * @param  \string[]                                        $cssClasses 
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
	 * @param  \string[]                                        $groupLabelCssClasses
	 * Css class or classes for group label as array of strings.
	 * 
	 * @param  array                                            $groupLabelAttrs
	 * Any additional attributes for group label, defined
	 * as key (for attribute name) and value (for attribute value).
	 * 
	 * @param  int                                              $minOptions
	 * Minimum options count to select. 
	 * Default value is `NULL` to not limit anything.
	 * @param  int                                              $maxOptions
	 * Maximum options count to select. 
	 * Default value is `NULL` to not limit anything.
	 * @param  string                                           $requiredBubbleMessage
	 * Field is required bubble message for javascript.
	 * @param  string                                           $minOptionsBubbleMessage
	 * Minimum options bubble message for javascript.
	 * @param  string                                           $maxOptionsBubbleMessage
	 * Maximum options bubble message for javascript.
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

		array $options = [],
		$translateOptions = TRUE,
		array $optionsLoader = [],

		array $groupLabelCssClasses = [],

		array $groupLabelAttrs = [],

		$minOptions = NULL,
		$maxOptions = NULL,
		$requiredBubbleMessage = NULL,
		$minOptionsBubbleMessage = NULL,
		$maxOptionsBubbleMessage = NULL
	) {
		$this->consolidateCfg($cfg, func_get_args(), func_num_args());
		parent::__construct($cfg);
		static::$templates = (object) array_merge(
			(array) parent::$templates, 
			(array) self::$templates
		);
	}

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Form` after field
	 * is added into form by `$form->AddField();` method. 
	 * Do not use this method even if you don't develop any form field group.
	 * - Check if field has any name, which is required.
	 * - Set up form and field id attribute by form id and field name.
	 * - Set up required.
	 * - Check if there are any options for current controls group.
	 * - Check if there are defined validators if there are defined minimum or maximum selected options.
	 * @param  \MvcCore\Ext\Form $form
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\Fields\CheckboxGroup
	 */
	public function SetForm (\MvcCore\Ext\IForm $form) {
		/** @var \MvcCore\Ext\Forms\Fields\CheckboxGroup $this */
		parent::SetForm($form);
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
		/** @var \MvcCore\Ext\Forms\Fields\CheckboxGroup $this */
		$result = [
			'multiple'		=> TRUE, 
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
	 * - Set up field render mode.
	 * - Set up translation boolean.
	 * - Translate label property if any.
	 * - Translate all option texts if necessary.
	 * - Translate browser bubble messages if necessary.
	 * - Add supporting javascripts if necessary.
	 * @return void
	 */
	public function PreDispatch () {
		/** @var \MvcCore\Ext\Forms\Fields\CheckboxGroup $this */
		parent::PreDispatch();
		$minOptsDefined = $this->minOptions !== NULL;
		$maxOptsDefined = $this->maxOptions !== NULL;
		$addSupportingJavascript = $this->required || $minOptsDefined || $maxOptsDefined;
		if (!$addSupportingJavascript) return;
		$form = & $this->form;
		// add necessary error messages
		$this->requiredBubbleMessage = strip_tags($this->translateAndFormatValidationError(
			($this->requiredBubbleMessage
				? $this->requiredBubbleMessage
				: static::$errorMessages[static::ERROR_REQUIRED_BUBBLE])
		));
		$this->minOptionsBubbleMessage = strip_tags($this->translateAndFormatValidationError(
			($this->minOptionsBubbleMessage
				? $this->minOptionsBubbleMessage
				: static::$errorMessages[static::ERROR_MIN_OPTIONS_BUBBLE]),
			[$minOptsDefined ? $this->minOptions : 1]
		));
		$this->maxOptionsBubbleMessage = strip_tags($this->translateAndFormatValidationError(
			($this->maxOptionsBubbleMessage
				? $this->maxOptionsBubbleMessage
				: static::$errorMessages[static::ERROR_MAX_OPTIONS_BUBBLE]),
			[$maxOptsDefined ? $this->maxOptions : count($this->options)]
		));
		$form->AddJsSupportFile(
			$this->jsSupportingFile, 
			$this->jsClassName, 
			[
				$this->name . '[]', 
				$this->required,
				$this->minOptions,
				$this->maxOptions,
				$this->requiredBubbleMessage,
				$this->minOptionsBubbleMessage,
				$this->maxOptionsBubbleMessage,
				$this->maxOptionsClassName
			]
		);
	}

	/**
	 * Complete and return semi-finished strings for rendering by field key and option:
	 * - Label text string.
	 * - Label attributes string.
	 * - Control attributes string.
	 * @param  string       $key
	 * @param  string|array $option
	 * @return array
	 */
	protected function renderControlItemCompleteAttrsClassesAndText ($key, & $option) {
		/** @var \MvcCore\Ext\Forms\Fields\CheckboxGroup $this */
		$optionType = gettype($option);
		$labelAttrsStr = '';
		$controlAttrsStr = '';
		$itemLabelText = '';
		$originalRequired = $this->required;
		$this->required = FALSE;
		if ($optionType == 'string') {
			$itemLabelText = $option ? $option : $key;
			$labelAttrsStr = $this->renderLabelAttrsWithFieldVars();
			$controlAttrsMerged = $this->controlAttrs;
			if ($this->minOptions !== NULL)
				$controlAttrsMerged = array_merge($controlAttrsMerged, ['data-min-selected-options' => $this->minOptions,]);
			if ($this->maxOptions !== NULL)
				$controlAttrsMerged = array_merge($controlAttrsMerged, ['data-max-selected-options' => $this->maxOptions,]);
			$controlAttrsStr = $this->renderAttrsWithFieldVars(
				[], $controlAttrsMerged, $this->cssClasses, TRUE
			);
		} else if ($optionType == 'array') {
			$itemLabelText = isset($option['text']) ? $option['text'] : $key;
			$attrsArr = $this->controlAttrs;
			$classArr = $this->cssClasses;
			if (isset($option['attrs']) && gettype($option['attrs']) == 'array') {
				$attrsArr = array_merge($this->controlAttrs, $option['attrs']);
			}
			if ($this->minOptions !== NULL)
				$attrsArr = array_merge($attrsArr, ['data-min-selected-options' => $this->minOptions,]);
			if ($this->maxOptions !== NULL)
				$attrsArr = array_merge($attrsArr, ['data-max-selected-options' => $this->maxOptions,]);
			if (isset($option['class'])) {
				$classArrParam = [];
				$cssClass = $option['class'];
				if (is_array($cssClass)) {
					$classArrParam = $cssClass;
				} else if (is_string($cssClass)) {
					$classArrParam = explode(' ', $cssClass);
				}
				foreach ($classArrParam as $clsValue) 
					if ($clsValue) $classArr[] = $clsValue;
			}
			$labelAttrsStr = $this->renderAttrsWithFieldVars(
				[], $attrsArr, $classArr, FALSE
			);
			$controlAttrsStr = $this->renderAttrsWithFieldVars(
				[], $attrsArr, $classArr, TRUE
			);
		}
		$this->required = $originalRequired;
		return [$itemLabelText, $labelAttrsStr, $controlAttrsStr];
	}
}
