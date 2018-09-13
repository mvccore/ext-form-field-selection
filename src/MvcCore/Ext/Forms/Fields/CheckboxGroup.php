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
 * Responsibility: init, predispatch and render group of `input`s 
 *				   with `type` as `checkbox`, with configuration to 
 *				   select minimum and maximum count of values and 
 *				   required option. 
 *				   `CheckboxGroup` has it's own validator to check if 
 *				   submitted values are presented in configured by 
 *				   default and it's own validator to check minimum or 
 *				   maximum count of selected options.
 */
class CheckboxGroup 
	extends		\MvcCore\Ext\Forms\FieldsGroup 
	implements	\MvcCore\Ext\Forms\Fields\IMinMaxOptions
{
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
	protected $type = 'checkbox';
	
	/**
	 * Validators: 
	 * - `ValueInOptions` - to validate if submitted string(s) 
	 *						are presented in select options keys.
	 * @var string[]|\Closure[]
	 */
	protected $validators = ["ValueInOptions"];

	/**
	 * Supporting javascript full javascript class name.
	 * If you want to use any custom supporting javascript prototyped class
	 * for any additional purposes for your custom field, you need to use
	 * `$field->jsSupportingFile` property to define path to your javascript file
	 * relatively from configured `\MvcCore\Ext\Form::SetJsSupportFilesRootDir(...);`
	 * value. Than you have to add supporting javascript file path into field form 
	 * in `$field->PreDispatch();` method to render those files immediatelly after form
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
	 * in `$field->PreDispatch();` method to render those files immediatelly after form
	 * (once) or by any external custom assets renderer configured by:
	 * `$form->SetJsSupportFilesRenderer(...);` method.
	 * Or you can add your custom supporting javascript files into response by your 
	 * own and also you can run your helper javascripts also by your own. Is up to you.
	 * `NULL` by default.
	 * @var string
	 */
	protected $jsSupportingFile = \MvcCore\Ext\Forms\IForm::FORM_ASSETS_DIR_REPLACEMENT . '/fields/checkbox-group.js';

	/**
	 * Maximum options specific css class for supporting javascript code.
	 * @var string
	 */
	protected $maxOptionsClassName = 'max-selected-options';

	/**
	 * Standard field template strings for natural rendering a `control`.
	 * @var string
	 */
	protected static $templates = [
		'control'	=> '<input id="{id}" name="{name}[]" type="{type}" value="{value}"{checked}{attrs} />',
	];

	/**
	 * Create new form `<input type="checkbx" />` group control instance.
	 * @param array $cfg Config array with public properties and it's 
	 *					 values which you want to configure, presented 
	 *					 in camel case properties names syntax.
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\Fields\CheckboxGroup|\MvcCore\Ext\Forms\IField
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
	 * is added into form by `$form->AddField();` method. 
	 * Do not use this method even if you don't develop any form field group.
	 * - Check if field has any name, which is required.
	 * - Set up form and field id attribute by form id and field name.
	 * - Set up required.
	 * - Check if there are any options for current controls group.
	 * - Check if there are defined validators if there are defined minimum or maximum selected options.
	 * @param \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm $form
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\Fields\Select|\MvcCore\Ext\Forms\IField
	 */
	public function & SetForm (\MvcCore\Ext\Forms\IForm & $form) {
		parent::SetForm($form);
		// add minimum/maximum options count validator if necessary
		$this->setFormMinMaxOptions();
		return $this;
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
	 * - Label attributes string string.
	 * - Control attributes string.
	 * @param string	   $key
	 * @param string|array $option
	 * @return array
	 */
	protected function renderControlItemCompleteAttrsClassesAndText ($key, & $option) {
		$optionType = gettype($option);
		$labelAttrsStr = '';
		$controlAttrsStr = '';
		$itemLabelText = '';
		$originalRequired = $this->required;
		if ($this->type == 'checkbox') 
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
				$cssClassType = gettype($option['class']);
				if ($cssClassType == 'array') {
					$classArrParam = $option['class'];
				} else if ($cssClassType == 'string') {
					$classArrParam = explode(' ', $option['class']);
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
		if ($this->type == 'checkbox') 
			$this->required = $originalRequired;
		return [$itemLabelText, $labelAttrsStr, $controlAttrsStr];
	}
}
