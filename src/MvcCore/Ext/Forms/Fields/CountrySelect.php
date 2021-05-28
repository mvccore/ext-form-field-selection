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
 *                 list for multiple selection with options as all existing 
 *                 world states or only filtered world states.
 *                 `CountrySelect` field has it's own validator to check if 
 *                 submitted value is presented in configured options by default.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class CountrySelect extends \MvcCore\Ext\Forms\Fields\Select {

	/**
	 * Possible value: `country-select`, not used in HTML code for this field.
	 * @var string
	 */
	protected $type = 'country-select';
	
	/**
	 * Translate English state names. Default value is `FALSE`.
	 * @var bool
	 */
	protected $translate = FALSE;
	
	/**
	 * All existing country codes and English state names.
	 * Keys are country codes in upper case, values are English state names.
	 * this array is automatically used to render all select options. If there 
	 * is configured any filtering to filter displayed countries, only selected
	 * states are rendered. Use method `$field->FilterOptions();` or constructor
	 * `$cfg` array with record `filter` as array with upper case country codes 
	 * array to render only.
	 * @var array
	 */
	protected static $allOptions = [
		'AF' => 'Afghanistan',			'AX' => 'Åland Islands',		'AL' => 'Albania',
		'DZ' => 'Algeria',				'AS' => 'American Samoa',		'AD' => 'Andorra',
		'AO' => 'Angola',				'AI' => 'Anguilla',				'AQ' => 'Antarctica',
		'AG' => 'Antigua and Barbuda',	'AR' => 'Argentina',			'AM' => 'Armenia',
		'AW' => 'Aruba',				'AU' => 'Australia',			'AT' => 'Austria',
		'AZ' => 'Azerbaijan',			'BS' => 'Bahamas',				'BH' => 'Bahrain',
		'BD' => 'Bangladesh',			'BB' => 'Barbados',				'BY' => 'Belarus',
		'BE' => 'Belgium',				'BZ' => 'Belize',				'BJ' => 'Benin',
		'BM' => 'Bermuda',				'BT' => 'Bhutan',				'BO' => 'Bolivia, Plurinational State of',
		'BQ' => 'Bonaire, Sint Eustatius and Saba','BA' => 'Bosnia and Herzegovina','BW' => 'Botswana',
		'BV' => 'Bouvet Island',		'BR' => 'Brazil',				'IO' => 'British Indian Ocean Territory',
		'BN' => 'Brunei Darussalam',	'BG' => 'Bulgaria',				'BF' => 'Burkina Faso',
		'BI' => 'Burundi',				'KH' => 'Cambodia',				'CM' => 'Cameroon',
		'CA' => 'Canada',				'CV' => 'Cape Verde',			'KY' => 'Cayman Islands',
		'CF' => 'Central African Republic',	'TD' => 'Chad',				'CL' => 'Chile',
		'CN' => 'China',				'CX' => 'Christmas Island',		'CC' => 'Cocos (Keeling) Islands',
		'CO' => 'Colombia',				'KM' => 'Comoros',				'CG' => 'Congo',
		'CD' => 'Congo, the Democratic Republic of the','CK' => 'Cook Islands','CR' => 'Costa Rica',
		'CI' => 'Côte d\'Ivoire',		'HR' => 'Croatia',				'CU' => 'Cuba',
		'CW' => 'Curaçao',				'CY' => 'Cyprus',				'CZ' => 'Czech Republic',
		'DK' => 'Denmark',				'DJ' => 'Djibouti',				'DM' => 'Dominica',
		'DO' => 'Dominican Republic',	'EC' => 'Ecuador',				'EG' => 'Egypt',
		'SV' => 'El Salvador',			'GQ' => 'Equatorial Guinea',	'ER' => 'Eritrea',
		'EE' => 'Estonia',				'ET' => 'Ethiopia',				'FK' => 'Falkland Islands (Malvinas)',
		'FO' => 'Faroe Islands',		'FJ' => 'Fiji',					'FI' => 'Finland',
		'FR' => 'France',				'GF' => 'French Guiana',		'PF' => 'French Polynesia',
		'TF' => 'French Southern Territories',	'GA' => 'Gabon',		'GM' => 'Gambia',
		'GE' => 'Georgia',				'DE' => 'Germany',				'GH' => 'Ghana',
		'GI' => 'Gibraltar',			'GR' => 'Greece',				'GL' => 'Greenland',
		'GD' => 'Grenada',				'GP' => 'Guadeloupe',			'GU' => 'Guam',
		'GT' => 'Guatemala',			'GG' => 'Guernsey',				'GN' => 'Guinea',
		'GW' => 'Guinea-Bissau',		'GY' => 'Guyana',				'HT' => 'Haiti',
		'HM' => 'Heard Island and McDonald Islands','VA' => 'Holy See (Vatican City State)','HN' => 'Honduras',
		'HK' => 'Hong Kong',			'HU' => 'Hungary',				'IS' => 'Iceland',
		'IN' => 'India',				'ID' => 'Indonesia',			'IR' => 'Iran, Islamic Republic of',
		'IQ' => 'Iraq',					'IE' => 'Ireland',				'IM' => 'Isle of Man',
		'IL' => 'Israel',				'IT' => 'Italy',				'JM' => 'Jamaica',
		'JP' => 'Japan',				'JE' => 'Jersey',				'JO' => 'Jordan',
		'KZ' => 'Kazakhstan',			'KE' => 'Kenya',				'KI' => 'Kiribati',
		'KP' => 'Korea, Democratic People\'s Republic of','KR' => 'Korea, Republic of','KW' => 'Kuwait',
		'KG' => 'Kyrgyzstan',			'LA' => 'Lao People\'s Democratic Republic','LV' => 'Latvia',
		'LB' => 'Lebanon',				'LS' => 'Lesotho',				'LR' => 'Liberia',
		'LY' => 'Libya',				'LI' => 'Liechtenstein',		'LT' => 'Lithuania',
		'LU' => 'Luxembourg',			'MO' => 'Macao',				'MK' => 'Macedonia, the former Yugoslav Republic of',
		'MG' => 'Madagascar',			'MW' => 'Malawi',				'MY' => 'Malaysia',
		'MV' => 'Maldives',				'ML' => 'Mali',					'MT' => 'Malta',
		'MH' => 'Marshall Islands',		'MQ' => 'Martinique',			'MR' => 'Mauritania',
		'MU' => 'Mauritius',			'YT' => 'Mayotte',				'MX' => 'Mexico',
		'FM' => 'Micronesia, Federated States of','MD' => 'Moldova, Republic of','MC' => 'Monaco',
		'MN' => 'Mongolia',				'ME' => 'Montenegro',			'MS' => 'Montserrat',
		'MA' => 'Morocco',				'MZ' => 'Mozambique',			'MM' => 'Myanmar',
		'NA' => 'Namibia',				'NR' => 'Nauru',				'NP' => 'Nepal',
		'NL' => 'Netherlands',			'NC' => 'New Caledonia',		'NZ' => 'New Zealand',
		'NI' => 'Nicaragua',			'NE' => 'Niger',				'NG' => 'Nigeria',
		'NU' => 'Niue',					'NF' => 'Norfolk Island',		'MP' => 'Northern Mariana Islands',
		'NO' => 'Norway',				'OM' => 'Oman',					'PK' => 'Pakistan',
		'PW' => 'Palau',				'PS' => 'Palestinian Territory, Occupied','PA' => 'Panama',
		'PG' => 'Papua New Guinea',		'PY' => 'Paraguay',				'PE' => 'Peru',
		'PH' => 'Philippines',			'PN' => 'Pitcairn',				'PL' => 'Poland',
		'PT' => 'Portugal',				'PR' => 'Puerto Rico',			'QA' => 'Qatar',
		'RE' => 'Réunion',				'RO' => 'Romania',				'RU' => 'Russian Federation',
		'RW' => 'Rwanda',				'BL' => 'Saint Barthélemy',		'SH' => 'Saint Helena, Ascension and Tristan da Cunha',
		'KN' => 'Saint Kitts and Nevis','LC' => 'Saint Lucia',			'MF' => 'Saint Martin (French part)',
		'PM' => 'Saint Pierre and Miquelon','VC' => 'Saint Vincent and the Grenadines','WS' => 'Samoa',
		'SM' => 'San Marino',			'ST' => 'Sao Tome and Principe','SA' => 'Saudi Arabia',
		'SN' => 'Senegal',				'RS' => 'Serbia',				'SC' => 'Seychelles',
		'SL' => 'Sierra Leone',			'SG' => 'Singapore',			'SX' => 'Sint Maarten (Dutch part)',
		'SK' => 'Slovakia',				'SI' => 'Slovenia',				'SB' => 'Solomon Islands',
		'SO' => 'Somalia',				'ZA' => 'South Africa',			'GS' => 'South Georgia and the South Sandwich Islands',
		'SS' => 'South Sudan',			'ES' => 'Spain',				'LK' => 'Sri Lanka',
		'SD' => 'Sudan',				'SR' => 'Suriname',				'SJ' => 'Svalbard and Jan Mayen',
		'SZ' => 'Swaziland',			'SE' => 'Sweden',				'CH' => 'Switzerland',
		'SY' => 'Syrian Arab Republic',	'TW' => 'Taiwan, Province of China','TJ' => 'Tajikistan',
		'TZ' => 'Tanzania, United Republic of','TH' => 'Thailand',		'TL' => 'Timor-Leste',
		'TG' => 'Togo',					'TK' => 'Tokelau',				'TO' => 'Tonga',
		'TT' => 'Trinidad and Tobago',	'TN' => 'Tunisia',				'TR' => 'Turkey',
		'TM' => 'Turkmenistan',			'TC' => 'Turks and Caicos Islands','TV' => 'Tuvalu',
		'UG' => 'Uganda',				'UA' => 'Ukraine',				'AE' => 'United Arab Emirates',
		'GB' => 'United Kingdom',		'US' => 'United States',		'UM' => 'United States Minor Outlying Islands',
		'UY' => 'Uruguay',				'UZ' => 'Uzbekistan',			'VU' => 'Vanuatu',
		'VE' => 'Venezuela, Bolivarian Republic of','VN' => 'Viet Nam',	'VG' => 'Virgin Islands, British',
		'VI' => 'Virgin Islands, U.S.',	'WF' => 'Wallis and Futuna',	'EH' => 'Western Sahara',
		'YE' => 'Yemen',				'ZM' => 'Zambia',				'ZW' => 'Zimbabwe',
	];

	/**
	 * Return country code in upper case.
	 * @return string
	 */
	public function GetValue () {
		/** @var \MvcCore\Ext\Forms\Fields\CountrySelect $this */
		return $this->value;
	}

	/**
	 * Set country code value. Given country code will be automatically converted to upper case.
	 * @param  string $countryCode 
	 * @return \MvcCore\Ext\Forms\Fields\CountrySelect
	 */
	public function SetValue ($countryCode) {
		/** @var \MvcCore\Ext\Forms\Fields\CountrySelect $this */
		$this->value = strtoupper($countryCode);
		return $this;
	}

	/**
	 * Get all existing country codes as array with keys as upper cased 
	 * country codes and values as not translated English state names.
	 * @return array
	 */
	public static function & GetAllOptions () {
		return static::$allOptions;
	}

	/**
	 * Set all existing country codes as array with keys as upper cased 
	 * country codes and values as not translated English state names.
	 * Given value will be automatically used as select options, if there 
	 * will not be configured any filtering to filter displayed countries.
	 * @param array $allOptions 
	 */
	public static function SetAllOptions ($allOptions = []) {
		static::$allOptions = $allOptions;
	}

	/**
	 * Filter displayed countries to not show every time all existing 
	 * countries in the world. Given country codes will be automatically
	 * converted to upper case.
	 * @param  \string[] $countryCodes Array of country codes strings to rendered only, not to render all existing states.
	 * @return \MvcCore\Ext\Forms\Fields\CountrySelect
	 */
	public function FilterOptions ($countryCodes = []) {
		/** @var \MvcCore\Ext\Forms\Fields\CountrySelect $this */
		$options = [];
		foreach ($countryCodes as $countryCode) {
			$countryCode = strtoupper($countryCode);
			if (isset(static::$allOptions[$countryCode])) {
				$options[$countryCode] = static::$allOptions[$countryCode];
			} else {
				$options[$countryCode] = $countryCode;
			}
		}
		$this->options = & $options;
		return $this;
	}
	
	/**
	 * Create new form country `<select>` control instance.
	 * If there is record under `filter` key in `$cfg` array argument,
	 * it's used for method $field->FilterOptions();` method.
	 * 
	 * @param  array            $cfg
	 * Config array with public properties and it's
	 * values which you want to configure, presented
	 * in camel case properties names syntax.
	 * 
	 * @param  string           $name 
	 * Form field specific name, used to identify submitted value.
	 * This value is required for all form fields.
	 * @param  string           $type 
	 * Fixed field order number, null by default.
	 * @param  int              $fieldOrder
	 * Form field type, used in `<input type="...">` attribute value.
	 * Every typed field has it's own string value, but base field type 
	 * `\MvcCore\Ext\Forms\Field` has `NULL`.
	 * @param  string|\string[] $value 
	 * Form field value. It could be string or array, int or float, it depends
	 * on field implementation. Default value is `NULL`.
	 * @param  string           $title 
	 * Field title, global HTML attribute, optional.
	 * @param  string           $translate 
	 * Boolean flag about field visible texts and error messages translation.
	 * This flag is automatically assigned from `$field->form->GetTranslate();` 
	 * flag in `$field->Init();` method.
	 * @param  string           $translateTitle 
	 * Boolean to translate title text, `TRUE` by default.
	 * @param  array            $cssClasses 
	 * Form field HTML element css classes strings.
	 * Default value is an empty array to not render HTML `class` attribute.
	 * @param  array            $controlAttrs 
	 * Collection with field HTML element additional attributes by array keys/values.
	 * Do not use system attributes as: `id`, `name`, `value`, `readonly`, `disabled`, `class` ...
	 * Those attributes has it's own configurable properties by setter methods or by constructor config array.
	 * HTML field elements are meant: `<input>, <button>, <select>, <textarea> ...`. 
	 * Default value is an empty array to not render any additional attributes.
	 * @param  array            $validators 
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
	 * @param  string           $accessKey
	 * The access key global attribute provides a hint for generating
	 * a keyboard shortcut for the current element. The attribute 
	 * value must consist of a single printable character (which 
	 * includes accented and other characters that can be generated 
	 * by the keyboard).
	 * @param  bool             $autoFocus
	 * This Boolean attribute lets you specify that a form control should have input
	 * focus when the page loads. Only one form-associated element in a document can
	 * have this attribute specified. 
	 * @param  bool             $disabled
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
	 * @param  bool             $readOnly
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
	 * @param  bool             $required
	 * Form field attribute `required`, determination
	 * if control will be required to complete any value by user.
	 * This flag is also used for submit checking. Default value is `NULL`
	 * to not require any field value. If form has configured it's property
	 * `$form->GetDefaultRequired()` to `TRUE` and this value is `NULL`, field
	 * will be automatically required by default form configuration.
	 * @param  int|string       $tabIndex
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
	 * @param  string           $label
	 * Control label visible text. If field form has configured any translator, translation 
	 * will be processed automatically before rendering process. Default value is `NULL`.
	 * @param  bool             $translateLabel
	 * Boolean to translate label text, `TRUE` by default.
	 * @param  string           $labelSide
	 * Label side from rendered field - location where label will be rendered.
	 * By default `$this->labelSide` is configured to `left`.
	 * If you want to reconfigure it to different side,
	 * the only possible value is `right`.
	 * You can use constants:
	 * - `\MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT`
	 * - `\MvcCore\Ext\Forms\IField::LABEL_SIDE_RIGHT`
	 * @param  int              $renderMode
	 * Rendering mode flag how to render field and it's label.
	 * Default value is `normal` to render label and field, label 
	 * first or field first by another property `$field->labelSide = 'left' | 'right';`.
	 * But if you want to render label around field or if you don't want
	 * to render any label, you can change this with constants (values):
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_NORMAL`       - `<label /><input />`
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_LABEL_AROUND` - `<label><input /></label>`
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_NO_LABEL`     - `<input />`
	 * @param  array            $labelAttrs
	 * Collection with `<label>` HTML element additional attributes by array keys/values.
	 * Do not use system attributes as: `id`,`for` or `class`, those attributes has it's own 
	 * configurable properties by setter methods or by constructor config array. Label `class` 
	 * attribute has always the same css classes as it's field automatically. 
	 * Default value is an empty array to not render any additional attributes.
	 * 
	 * @param  string           $autoComplete 
	 * Attribute indicates if the input can be automatically completed 
	 * by the browser, usually by remembering previous values the user 
	 * has entered. Possible values: `off`, `on`, `name`, `email`, 
	 * `username`, `country`, `postal-code` and many more...
	 * 
	 * @param  bool             $multiple
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
	 * @param  array            $options
	 * Form group control options to render more sub-control attributes for specified
	 * submitted values (array keys). This property configuration is required.
	 * @param  bool             $translateOptions
	 * Boolean about to translate options texts, default `TRUE` to translate.
	 * @param  array            $optionsLoader
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
	 * @param  int              $minOptions
	 * Minimum options count to select. 
	 * Default value is `NULL` to not limit anything.
	 * @param  int              $maxOptions
	 * Maximum options count to select. 
	 * Default value is `NULL` to not limit anything.
	 * 
	 * @param  string           $nullOptionText 
	 * This attribute is a text placeholder for `<select>` controls,
	 * when no option is selected yet. Typically: `--- please select an option ---`.
	 * It's rendered every time this placeholder has any text value, doesn't matter,
	 * if `<select>` tag has already any value selected or not yet . It's rendered usually 
	 * as first `<option>` sub-element with an empty value, as `disabled` and `selected` 
	 * `<option>` tag. `NULL` value means no placeholder `<option>` tag will be rendered.
	 * @param  bool             $translateNullOptionText
	 * Boolean to translate placeholder text, `TRUE` by default.
	 * 
	 * @param  int              $size
	 * If the field is `<input>`, this attribute is initial size of the control. Starting in HTML5, 
	 * this attribute applies only when the `type` attribute is set to `text`, `search`, `tel`, `url`, 
	 * `email`, or `password`, otherwise it is ignored. The `size` must be an integer greater than zero. 
	 * The default browser`s value is 20.
	 * If the field is `<select>`, this attribute is presented as a scrolling list box (e.g. when 
	 * `multiple` attribute is specified to `TRUE`), this attribute represents the number of rows in 
	 * the list that should be visible at one time. Browsers are not required to present a select element 
	 * as a scrolled list box. The default browser`s value is `0`.
	 * 
	 * @param  string           $wrapper
	 * Html code wrapper, wrapper has to contain replacement in string 
	 * form: `{control}`. Around this substring you can wrap any HTML 
	 * code you want. Default wrapper values is: `'{control}'`.
	 * 
	 * @param  array            $filter
	 * Filter displayed countries to not show every time all existing 
	 * countries in the world. Given country codes will be automatically
	 * converted to upper case.
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

		$wrapper = NULL,

		$filter = NULL
	) {
		$this->consolidateCfg($cfg, func_get_args(), func_num_args());
		parent::__construct($cfg);
		if (isset($cfg['filter'])) 
			$this->FilterOptions($cfg['filter']);
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
	 * @return \MvcCore\Ext\Forms\Fields\CountrySelect
	 */
	public function SetForm (\MvcCore\Ext\IForm $form) {
		/** @var \MvcCore\Ext\Forms\Fields\CountrySelect $this */
		if ($this->form !== NULL) return $this;
		if (!$this->options) 
			$this->options = static::$allOptions;
		return parent::SetForm($form);
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
		/** @var \MvcCore\Ext\Forms\Fields\CountrySelect $this */
		$result = '';
		$valueTypeIsArray = is_array($this->value);
		if ($this->nullOptionText !== NULL && strlen((string) $this->nullOptionText) > 0) {
			// advanced configuration with key, text, CSS class, and any other attributes for single option tag
			$result .= $this->renderControlOptionsAdvanced(
				'', [
					'value'	=> '',
					'text'	=> htmlspecialchars_decode(htmlspecialchars($this->nullOptionText, ENT_QUOTES), ENT_QUOTES),
					'class'	=> 'country-none',
					//'attrs'	=> ['disabled' => 'disabled']
				], $valueTypeIsArray
			);
		}
		foreach ($this->options as $key => & $value) {
			// advanced configuration with key, text, CSS class, and any other attributes for single option tag
			$result .= $this->renderControlOptionsAdvanced($key, [
				'class'	=> 'country-' . strtolower($key),
				'text'	=> htmlspecialchars_decode(htmlspecialchars($value, ENT_QUOTES), ENT_QUOTES),
				'value'	=> htmlspecialchars_decode(htmlspecialchars($key, ENT_QUOTES), ENT_QUOTES),
			], $valueTypeIsArray);
		}
		return $result;
	}
}
