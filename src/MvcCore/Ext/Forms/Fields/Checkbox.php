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
 * Responsibility: init, predispatch and render `<input>` HTML element with 
 *				   type `checkbox`. `checkbox` field has it's own validator 
 *				   `SafeString` to clean string from base ASCII chars and 
 *				   some control chars by default. But validator `SafeString` 
 *				   doesn't prevent SQL injects and more.
 */
class Checkbox 
	extends		\MvcCore\Ext\Forms\Field
	implements	\MvcCore\Ext\Forms\Fields\IVisibleField, 
				\MvcCore\Ext\Forms\Fields\ILabel, 
				\MvcCore\Ext\Forms\Fields\IChecked
{
	use \MvcCore\Ext\Forms\Field\Props\VisibleField;
	use \MvcCore\Ext\Forms\Field\Props\Label;
	use \MvcCore\Ext\Forms\Field\Props\Checked;

	/**
	 * Possible values: `checkbox`.
	 * @var string
	 */
	protected $type = 'checkbox';
	
	/**
	 * Render label on right side from `<input type="checkbox" />` element.
	 * @var string
	 */
	protected $labelSide = \MvcCore\Ext\Forms\IField::LABEL_SIDE_RIGHT;

	/**
	 * Validators: 
	 * - `SafeString` - remove from submitted value base ASCII characters from 0 to 31 incl. 
	 *					(first column) and escape special characters: `& " ' < > | = \ %`.
	 *					This validator is not prevent SQL inject attacks!
	 * @var string[]|\Closure[]
	 */
	protected $validators = ['SafeString'];

	/**
	 * Standard field template strings for natural 
	 * rendering - `control`, `togetherLabelLeft` and `togetherLabelRight`.
	 * @var string
	 */
	protected static $templates = [
		'control'			=> '<input id="{id}" name="{name}" type="checkbox" value="{value}"{attrs} />',
		'togetherLabelLeft'	=> '<label for="{id}"{attrs}><span>{label}</span>{control}</label>',
		'togetherLabelRight'=> '<label for="{id}"{attrs}>{control}<span>{label}</span></label>',
	];
	
	/**
	 * Create new form control instance.
	 * @param array $cfg Config array with public properties and it's 
	 *					 values which you want to configure, presented 
	 *					 in camel case properties names syntax.
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\Fields\Checkbox|\MvcCore\Ext\Forms\IField
	 */
	public function __construct(array $cfg = []) {
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
	 * - Set up tabindex if necessary.
	 * @return void
	 */
	public function PreDispatch () {
		parent::PreDispatch();
		$this->preDispatchTabIndex();
	}
	
	/**
	 * This INTERNAL method is called from `\MvcCore\Ext\Forms\Field\Rendering` 
	 * in rendering process. Do not use this method even if you don't develop any form field.
	 * 
	 * Render control tag only without label or specific errors.
	 * @return string
	 */
	public function RenderControl () {
		$attrsStr = $this->renderControlAttrsWithFieldVars();
		if (!$this->form->GetFormTagRenderingStatus()) 
			$attrsStr .= (strlen($attrsStr) > 0 ? ' ' : '')
				. 'form="' . $this->form->GetId() . '"';
		$viewClass = $this->form->GetViewClass();
		if ($this->checked === NULL) 
			$this->checked = static::GetCheckedByValue($this->value);
		$valueStr = htmlspecialchars($this->value, ENT_QUOTES);
		if (!$valueStr) 
			$valueStr = 'true';
		if ($this->checked) 
			$valueStr .= '" checked="checked';
		return $viewClass::Format(static::$templates->control, [
			'id'		=> $this->id,
			'name'		=> $this->name,
			'value'		=> $valueStr,
			'attrs'		=> strlen($attrsStr) > 0 ? ' ' . $attrsStr : '',
		]);
	}
}
