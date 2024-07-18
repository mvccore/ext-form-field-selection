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
 * Responsibility: init, pre-dispatch and render `<input>` HTML element with 
 *                 type `checkbox`. `checkbox` field has it's own validator 
 *                 `SafeString` to clean string from base ASCII chars and 
 *                 some control chars by default. But validator `SafeString` 
 *                 doesn't prevent SQL injects and more.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class		Checkbox 
extends		\MvcCore\Ext\Forms\Field
implements	\MvcCore\Ext\Forms\Fields\IVisibleField, 
			\MvcCore\Ext\Forms\Fields\ILabel, 
			\MvcCore\Ext\Forms\Fields\IChecked {

	use \MvcCore\Ext\Forms\Field\Props\VisibleField;
	use \MvcCore\Ext\Forms\Field\Props\Label;
	use \MvcCore\Ext\Forms\Field\Props\Checked;
	use \MvcCore\Ext\Forms\Field\Props\Wrapper;

	/**
	 * Possible values: `checkbox`.
	 * @var string
	 */
	protected $type = 'checkbox';
	
	/**
	 * Render label on right side from `<input type="checkbox" />` element.
	 * @var string
	 */
	#protected $labelSide = \MvcCore\Ext\Forms\IField::LABEL_SIDE_RIGHT;

	/**
	 * Validators: 
	 * - `SafeString` - remove from submitted value base ASCII characters from 0 to 31 incl. 
	 *                  (first column) and escape special characters: `& " ' < > | = \ %`.
	 *                  This validator is not prevent SQL inject attacks!
	 * @var \string[]|\Closure[]
	 */
	protected $validators = ['SafeString'];

	/**
	 * Standard field template strings for natural 
	 * rendering - `control`, `togetherLabelLeft` and `togetherLabelRight`.
	 * @var \string[]|\stdClass
	 */
	protected static $templates = [
		'control'			=> '<input id="{id}" name="{name}" type="checkbox" value="{value}"{attrs} />',
		'togetherLabelLeft'	=> '<label for="{id}"{attrs}><span>{label}</span>{control}</label>',
		'togetherLabelRight'=> '<label for="{id}"{attrs}>{control}<span>{label}</span></label>',
	];
	
	/**
	 * Create new form `<input type="checkbox">` control instance.
	 * 
	 * @param  array                 $cfg
	 * Config array with public properties and it's
	 * values which you want to configure, presented
	 * in camel case properties names syntax.
	 * 
	 * @param  string                $name 
	 * Form field specific name, used to identify submitted value.
	 * This value is required for all form fields.
	 * @param  string                $type 
	 * Fixed field order number, null by default.
	 * @param  int                   $fieldOrder
	 * Form field type, used in `<input type="...">` attribute value.
	 * Every typed field has it's own string value, but base field type 
	 * `\MvcCore\Ext\Forms\Field` has `NULL`.
	 * @param  string|bool|int|float $value 
	 * Form field value. It could be string or array, int or float, it depends
	 * on field implementation. Default value is `NULL`.
	 * @param  string                $title 
	 * Field title, global HTML attribute, optional.
	 * @param  string                $translate 
	 * Boolean flag about field visible texts and error messages translation.
	 * This flag is automatically assigned from `$field->form->GetTranslate();` 
	 * flag in `$field->Init();` method.
	 * @param  string                $translateTitle 
	 * Boolean to translate title text, `TRUE` by default.
	 * @param  array                 $cssClasses 
	 * Form field HTML element css classes strings.
	 * Default value is an empty array to not render HTML `class` attribute.
	 * @param  array                 $controlAttrs 
	 * Collection with field HTML element additional attributes by array keys/values.
	 * Do not use system attributes as: `id`, `name`, `value`, `readonly`, `disabled`, `class` ...
	 * Those attributes has it's own configurable properties by setter methods or by constructor config array.
	 * HTML field elements are meant: `<input>, <button>, <select>, <textarea> ...`. 
	 * Default value is an empty array to not render any additional attributes.
	 * @param  array                 $validators 
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
	 * @param  string                $accessKey
	 * The access key global attribute provides a hint for generating
	 * a keyboard shortcut for the current element. The attribute 
	 * value must consist of a single printable character (which 
	 * includes accented and other characters that can be generated 
	 * by the keyboard).
	 * @param  bool                  $autoFocus
	 * This Boolean attribute lets you specify that a form control should have input
	 * focus when the page loads. Only one form-associated element in a document can
	 * have this attribute specified. 
	 * @param  bool                  $disabled
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
	 * @param  bool                  $readOnly
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
	 * @param  bool                  $required
	 * Form field attribute `required`, determination
	 * if control will be required to complete any value by user.
	 * This flag is also used for submit checking. Default value is `NULL`
	 * to not require any field value. If form has configured it's property
	 * `$form->GetDefaultRequired()` to `TRUE` and this value is `NULL`, field
	 * will be automatically required by default form configuration.
	 * @param  int|string            $tabIndex
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
	 * @param  string                $label
	 * Control label visible text. If field form has configured any translator, translation 
	 * will be processed automatically before rendering process. Default value is `NULL`.
	 * @param  bool                  $translateLabel
	 * Boolean to translate label text, `TRUE` by default.
	 * @param  string                $labelSide
	 * Label side from rendered field - location where label will be rendered.
	 * By default `$this->labelSide` is configured to `left`.
	 * If you want to reconfigure it to different side,
	 * the only possible value is `right`.
	 * You can use constants:
	 * - `\MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT`
	 * - `\MvcCore\Ext\Forms\IField::LABEL_SIDE_RIGHT`
	 * @param  int                   $renderMode
	 * Rendering mode flag how to render field and it's label.
	 * Default value is `normal` to render label and field, label 
	 * first or field first by another property `$field->labelSide = 'left' | 'right';`.
	 * But if you want to render label around field or if you don't want
	 * to render any label, you can change this with constants (values):
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_NORMAL`       - `<label /><input />`
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_LABEL_AROUND` - `<label><input /></label>`
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_NO_LABEL`     - `<input />`
	 * @param  array                 $labelAttrs
	 * Collection with `<label>` HTML element additional attributes by array keys/values.
	 * Do not use system attributes as: `id`,`for` or `class`, those attributes has it's own 
	 * configurable properties by setter methods or by constructor config array. Label `class` 
	 * attribute has always the same css classes as it's field automatically. 
	 * Default value is an empty array to not render any additional attributes.
	 * 
	 * @param  bool                  $cheked
	 * If `TRUE`, field will be rendered as checked, `FALSE` otherwise.
	 * If not set, checked flag will be automatically resolved by field value
	 * with method `static::GetCheckedByValue($checkbox->GetValue());`
	 * 
	 * @param  bool                  $boolMode
	 * 
	 * 
	 * @param  string                $wrapper
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

		$checked = NULL,
		$boolMode = NULL,
		$wrapper = NULL
	) {
		$this->consolidateCfg($cfg, func_get_args(), func_num_args());
		parent::__construct($cfg);
		static::$templates = (object) array_merge(
			(array) parent::$templates, 
			(array) self::$templates
		);
	}

	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Form` just before
	 * field is naturally rendered. It sets up field for rendering process.
	 * Do not use this method event if you don't develop any form field.
	 * - Set up field render mode if not defined.
	 * - Translate label text if necessary.
	 * - Set up tab-index if necessary.
	 * @return void
	 */
	public function PreDispatch () {
		$this->preDispatchChecked();
		parent::PreDispatch();
		$this->preDispatchTabIndex();
	}

	/**
	 * @inheritDoc
	 * @internal
	 * @template
	 * @param array $rawRequestParams Raw request params from MvcCore
	 *                                request object based on raw app
	 *                                input, `$_GET` or `$_POST`.
	 * @return bool|string|int|NULL
	 */
	public function Submit (array & $rawRequestParams = []) {
		$result = parent::Submit($rawRequestParams);
		if ($this->boolMode) {
			$newValue = FALSE;
			if (is_bool($result)) {
				$newValue = $result;
			} else if (is_string($result)) {
				$newValue = mb_strtolower($result) === 'true' || $result === '1';
			} else {
				$newValue = (bool) $result;
			}
			$result = $newValue;
		}
		return $result;
	}
	
	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\Field\Rendering` 
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 * 
	 * Render control tag only without label or specific errors.
	 * @return string
	 */
	public function RenderControl () {
		$attrsStrItems = [$this->RenderControlAttrsWithFieldVars()];
		if (!$this->form->GetFormTagRenderingStatus()) 
			$attrsStrItems[] = 'form="' . $this->form->GetId() . '"';
		$formViewClass = $this->form->GetViewClass();
		$view = $this->form->GetView() ?: $this->form->GetController()->GetView();
		if ($this->checked === NULL) 
			$this->checked = static::GetCheckedByValue($this->value);
		$valueStr = $view->EscapeAttr($this->value);
		if (!$valueStr || $this->boolMode) 
			$valueStr = 'true';
		if ($this->checked) 
			$valueStr .= '" checked="checked';
		/** @var \stdClass $templates */
		$templates = static::$templates;
		$result = $formViewClass::Format($templates->control, [
			'id'		=> $this->id,
			'name'		=> $this->name,
			'value'		=> $valueStr,
			'attrs'		=> count($attrsStrItems) > 0 ? ' ' . implode(' ', $attrsStrItems) : '',
		]);
		return $this->renderControlWrapper($result);
	}
}
