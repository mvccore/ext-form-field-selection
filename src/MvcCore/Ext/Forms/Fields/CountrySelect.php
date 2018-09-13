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
 *				   list for multiple selection with options as all existing 
 *				   world states or only filtered world states.
 *				   `CountrySelect` field has it's own validator to check if 
 *				   submitted value is presented in configured options by default.
 *				   
 */
class CountrySelect 
	extends \MvcCore\Ext\Forms\Fields\Select
{
	/**
	 * Possible value: `country-select`, not used in HTML code for this field.
	 * @var string
	 */
	protected $type = 'country-select';
	
	/**
	 * Translate english state names. Default value is `FALSE`.
	 * @var bool
	 */
	protected $translate = FALSE;
	
	/**
	 * All existing country codes and english state names.
	 * Keys are country codes in upper case, values are english state names.
	 * this array is automaticly used to render all select options. If there 
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
		return $this->value;
	}

	/**
	 * Set country code value. Given country code will be automaticly converted to uppercase.
	 * @param string $countryCode 
	 * @return \MvcCore\Ext\Forms\Fields\CountrySelect|\MvcCore\Ext\Forms\IField
	 */
	public function SetValue ($countryCode) {
		$this->value = strtoupper($countryCode);
		return $this;
	}

	/**
	 * Get all existing country codes as array with keys as upper cased 
	 * country codes and values as not translated english state names.
	 * @return array
	 */
	public static function & GetAllOptions () {
		return static::$allOptions;
	}

	/**
	 * Set all existing country codes as array with keys as upper cased 
	 * country codes and values as not translated english state names.
	 * Given value will be automaticly used as select options, if there 
	 * will not be configured any filtering to filter displayed countries.
	 * @param array $allOptions 
	 */
	public static function SetAllOptions ($allOptions = []) {
		static::$allOptions = $allOptions;
	}

	/**
	 * Filter displayed countries to not show everytime all eisting 
	 * countries in the world. Given country codes will be automaticly
	 * converted to upper case.
	 * @param \string[] $countryCodes Array of country codes strings to rendere only, not to render all existing states.
	 * @return \MvcCore\Ext\Forms\Fields\CountrySelect|\MvcCore\Ext\Forms\IField
	 */
	public function & FilterOptions ($countryCodes = []) {
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
	 * @param array $cfg Config array with public properties and it's 
	 *					 values which you want to configure, presented 
	 *					 in camel case properties names syntax.
	 * @throws \InvalidArgumentException
	 * @return \MvcCore\Ext\Forms\Fields\CountrySelect|\MvcCore\Ext\Forms\IField
	 */
	public function __construct(array $cfg = []) {
		parent::__construct($cfg);
		if (isset($cfg['filter'])) 
			$this->FilterOptions($cfg['filter']);
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
		if ($this->nullOptionText !== NULL && strlen((string) $this->nullOptionText) > 0) {
			// advanced configuration with key, text, cs class, and any other attributes for single option tag
			$result .= $this->renderControlOptionsAdvanced(
				'', [
					'value'	=> '',
					'text'	=> htmlspecialchars($this->nullOptionText, ENT_QUOTES),
					'class'	=> 'country-none',
					'attrs'	=> ['disabled' => 'disabled']
				], $valueTypeIsArray
			);
		}
		foreach ($this->options as $key => & $value) {
			// advanced configuration with key, text, cs class, and any other attributes for single option tag
			$result .= $this->renderControlOptionsAdvanced($key, [
				'class'	=> 'country-' . strtolower($key),
				'text'	=> htmlspecialchars($value, ENT_QUOTES),
				'value'	=> htmlspecialchars($key, ENT_QUOTES),
			], $valueTypeIsArray);
		}
		return $result;
	}
}
