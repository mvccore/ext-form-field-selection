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
 *                 world localizations or only filtered world localizations.
 *                 `LocalizationSelect` field has it's own validator to check if 
 *                 submitted value is presented in configured options by default.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class LocalizationSelect extends \MvcCore\Ext\Forms\Fields\Select {

	/**
	 * Possible value: `localization-select`, not used in HTML code for this field.
	 * @var string
	 */
	protected $type = 'localization-select';
	
	/**
	 * Translate English localizations names. Default value is `FALSE`.
	 * @var bool
	 */
	protected $translate = FALSE;
	
	/**
	 * All existing localization codes and English localizations names.
	 * Keys are language codes in lower case with optional alphabet after 
	 * underscore, than there is another underscore and country code in upper 
	 * case, values are English localizations names. This array is automatically 
	 * used to render all select options. If there is configured any filtering 
	 * to filter displayed localizations, only selected localizations are 
	 * rendered. Use method `$field->FilterOptions([...]);` or constructor `$cfg` 
	 * array with record `filter` as array with localization codes array to 
	 * render only.
	 * @var array
	 */
	protected static $allOptions = [
		"af_NA"		=> "Afrikaans (Namibia)",					"af_ZA"		=> "Afrikaans (South Africa)",
		"af"		=> "Afrikaans",								"ak_GH"		=> "Akan (Ghana)",
		"ak"		=> "Akan",									"sq_AL"		=> "Albanian (Albania)",
		"sq"		=> "Albanian",								"am_ET"		=> "Amharic (Ethiopia)",
		"am"		=> "Amharic",								"ar_DZ"		=> "Arabic (Algeria)",
		"ar_BH"		=> "Arabic (Bahrain)",						"ar_EG"		=> "Arabic (Egypt)",
		"ar_IQ"		=> "Arabic (Iraq)",							"ar_JO"		=> "Arabic (Jordan)",
		"ar_KW"		=> "Arabic (Kuwait)",						"ar_LB"		=> "Arabic (Lebanon)",
		"ar_LY"		=> "Arabic (Libya)",						"ar_MA"		=> "Arabic (Morocco)",
		"ar_OM"		=> "Arabic (Oman)",							"ar_QA"		=> "Arabic (Qatar)",
		"ar_SA"		=> "Arabic (Saudi Arabia)",					"ar_SD"		=> "Arabic (Sudan)",
		"ar_SY"		=> "Arabic (Syria)",						"ar_TN"		=> "Arabic (Tunisia)",
		"ar_AE"		=> "Arabic (United Arab Emirates)",			"ar_YE"		=> "Arabic (Yemen)",
		"ar"		=> "Arabic",								"hy_AM"		=> "Armenian (Armenia)",
		"hy"		=> "Armenian",								"as_IN"		=> "Assamese (India)",
		"as"		=> "Assamese",								"asa_TZ"	=> "Asu (Tanzania)",
		"asa"		=> "Asu",									"az_Cyrl"	=> "Azerbaijani (Cyrillic)",
		"az_Cyrl_AZ"=> "Azerbaijani (Cyrillic, Azerbaijan)",	"az_Latn"	=> "Azerbaijani (Latin)",
		"az_Latn_AZ"=> "Azerbaijani (Latin, Azerbaijan)",		"az"		=> "Azerbaijani",
		"bm_ML"		=> "Bambara (Mali)",						"bm"		=> "Bambara",
		"eu_ES"		=> "Basque (Spain)",						"eu"		=> "Basque",
		"be_BY"		=> "Belarusian (Belarus)",					"be"		=> "Belarusian",
		"bem_ZM"	=> "Bemba (Zambia)",						"bem"		=> "Bemba",
		"bez_TZ"	=> "Bena (Tanzania)",						"bez"		=> "Bena",
		"bn_BD"		=> "Bengali (Bangladesh)",					"bn_IN"		=> "Bengali (India)",
		"bn"		=> "Bengali",								"bs_BA"		=> "Bosnian (Bosnia and Herzegovina)",
		"bs"		=> "Bosnian",								"bg_BG"		=> "Bulgarian (Bulgaria)",
		"bg"		=> "Bulgarian",								"my_MM"		=> "Burmese (Myanmar [Burma])",
		"my"		=> "Burmese",								"yue_Hant_HK"=> "Cantonese (Traditional, Hong Kong SAR China)",
		"ca_ES"		=> "Catalan (Spain)",						"ca"		=> "Catalan",
		"tzm_Latn"	=> "Central Morocco Tamazight (Latin)",		"tzm_Latn_MA"=> "Central Morocco Tamazight (Latin, Morocco)",
		"tzm"		=> "Central Morocco Tamazight",				"chr_US"	=> "Cherokee (United States)",
		"chr"		=> "Cherokee",								"cgg_UG"	=> "Chiga (Uganda)",
		"cgg"		=> "Chiga",									"zh_Hans"	=> "Chinese (Simplified Han)",
		"zh_Hans_CN"=> "Chinese (Simplified Han, China)",		"zh_Hans_HK"=> "Chinese (Simplified Han, Hong Kong SAR China)",
		"zh_Hans_MO"=> "Chinese (Simplified Han, Macau SAR China)","zh_Hans_SG"=> "Chinese (Simplified Han, Singapore)",
		"zh_Hant"	=> "Chinese (Traditional Han)",				"zh_Hant_HK"=> "Chinese (Traditional Han, Hong Kong SAR China)",
		"zh_Hant_MO"=> "Chinese (Traditional Han, Macau SAR China)","zh_Hant_TW"=> "Chinese (Traditional Han, Taiwan)",
		"zh"		=> "Chinese",								"kw_GB"		=> "Cornish (United Kingdom)",
		"kw"		=> "Cornish",								"hr_HR"		=> "Croatian (Croatia)",
		"hr"		=> "Croatian",								"cs_CZ"		=> "Czech (Czech Republic)",
		"cs"		=> "Czech",									"da_DK"		=> "Danish (Denmark)",
		"da"		=> "Danish",								"nl_BE"		=> "Dutch (Belgium)",
		"nl_NL"		=> "Dutch (Netherlands)",					"nl"		=> "Dutch",
		"ebu_KE"	=> "Embu (Kenya)",							"ebu"		=> "Embu",
		"en_AS"		=> "English (American Samoa)",				"en_AU"		=> "English (Australia)",
		"en_BE"		=> "English (Belgium)",						"en_BZ"		=> "English (Belize)",
		"en_BW"		=> "English (Botswana)",					"en_CA"		=> "English (Canada)",
		"en_GU"		=> "English (Guam)",						"en_HK"		=> "English (Hong Kong SAR China)",
		"en_IN"		=> "English (India)",						"en_IE"		=> "English (Ireland)",
		"en_IL"		=> "English (Israel)",						"en_JM"		=> "English (Jamaica)",
		"en_MT"		=> "English (Malta)",						"en_MH"		=> "English (Marshall Islands)",
		"en_MU"		=> "English (Mauritius)",					"en_NA"		=> "English (Namibia)",
		"en_NZ"		=> "English (New Zealand)",					"en_MP"		=> "English (Northern Mariana Islands)",
		"en_PK"		=> "English (Pakistan)",					"en_PH"		=> "English (Philippines)",
		"en_SG"		=> "English (Singapore)",					"en_ZA"		=> "English (South Africa)",
		"en_TT"		=> "English (Trinidad and Tobago)",			"en_UM"		=> "English (U.S. Minor Outlying Islands)",
		"en_VI"		=> "English (U.S. Virgin Islands)",			"en_GB"		=> "English (United Kingdom)",
		"en_US"		=> "English (United States)",				"en_ZW"		=> "English (Zimbabwe)",
		"en"		=> "English",								"eo"		=> "Esperanto",
		"et_EE"		=> "Estonian (Estonia)",					"et"		=> "Estonian",
		"ee_GH"		=> "Ewe (Ghana)",							"ee_TG"		=> "Ewe (Togo)",
		"ee"		=> "Ewe",									"fo_FO"		=> "Faroese (Faroe Islands)",
		"fo"		=> "Faroese",								"fil_PH"	=> "Filipino (Philippines)",
		"fil"		=> "Filipino",								"fi_FI"		=> "Finnish (Finland)",
		"fi"		=> "Finnish",								"fr_BE"		=> "French (Belgium)",
		"fr_BJ"		=> "French (Benin)",						"fr_BF"		=> "French (Burkina Faso)",
		"fr_BI"		=> "French (Burundi)",						"fr_CM"		=> "French (Cameroon)",
		"fr_CA"		=> "French (Canada)",						"fr_CF"		=> "French (Central African Republic)",
		"fr_TD"		=> "French (Chad)",							"fr_KM"		=> "French (Comoros)",
		"fr_CG"		=> "French (Congo - Brazzaville)",			"fr_CD"		=> "French (Congo - Kinshasa)",
		"fr_CI"		=> "French (Côte d’Ivoire)",				"fr_DJ"		=> "French (Djibouti)",
		"fr_GQ"		=> "French (Equatorial Guinea)",			"fr_FR"		=> "French (France)",
		"fr_GA"		=> "French (Gabon)",						"fr_GP"		=> "French (Guadeloupe)",
		"fr_GN"		=> "French (Guinea)",						"fr_LU"		=> "French (Luxembourg)",
		"fr_MG"		=> "French (Madagascar)",					"fr_ML"		=> "French (Mali)",
		"fr_MQ"		=> "French (Martinique)",					"fr_MC"		=> "French (Monaco)",
		"fr_NE"		=> "French (Niger)",						"fr_RW"		=> "French (Rwanda)",
		"fr_RE"		=> "French (Réunion)",						"fr_BL"		=> "French (Saint Barthélemy)",
		"fr_MF"		=> "French (Saint Martin)",					"fr_SN"		=> "French (Senegal)",
		"fr_CH"		=> "French (Switzerland)",					"fr_TG"		=> "French (Togo)",
		"fr"		=> "French",								"ff_SN"		=> "Fulah (Senegal)",
		"ff"		=> "Fulah",									"gl_ES"		=> "Galician (Spain)",
		"gl"		=> "Galician",								"lg_UG"		=> "Ganda (Uganda)",
		"lg"		=> "Ganda",									"ka_GE"		=> "Georgian (Georgia)",
		"ka"		=> "Georgian",								"de_AT"		=> "German (Austria)",
		"de_BE"		=> "German (Belgium)",						"de_DE"		=> "German (Germany)",
		"de_LI"		=> "German (Liechtenstein)",				"de_LU"		=> "German (Luxembourg)",
		"de_CH"		=> "German (Switzerland)",					"de"		=> "German",
		"el_CY"		=> "Greek (Cyprus)",						"el_GR"		=> "Greek (Greece)",
		"el"		=> "Greek",									"gu_IN"		=> "Gujarati (India)",
		"gu"		=> "Gujarati",								"guz_KE"	=> "Gusii (Kenya)",
		"guz"		=> "Gusii",									"ha_Latn"	=> "Hausa (Latin)",
		"ha_Latn_GH"=> "Hausa (Latin, Ghana)",					"ha_Latn_NE"=> "Hausa (Latin, Niger)",
		"ha_Latn_NG"=> "Hausa (Latin, Nigeria)",				"ha"		=> "Hausa",
		"haw_US"	=> "Hawaiian (United States)",				"haw"		=> "Hawaiian",
		"he_IL"		=> "Hebrew (Israel)",						"he"		=> "Hebrew",
		"hi_IN"		=> "Hindi (India)",							"hi"		=> "Hindi",
		"hu_HU"		=> "Hungarian (Hungary)",					"hu"		=> "Hungarian",
		"is_IS"		=> "Icelandic (Iceland)",					"is"		=> "Icelandic",
		"ig_NG"		=> "Igbo (Nigeria)",						"ig"		=> "Igbo",
		"id_ID"		=> "Indonesian (Indonesia)",				"id"		=> "Indonesian",
		"ga_IE"		=> "Irish (Ireland)",						"ga"		=> "Irish",
		"it_IT"		=> "Italian (Italy)",						"it_CH"		=> "Italian (Switzerland)",
		"it"		=> "Italian",								"ja_JP"		=> "Japanese (Japan)",
		"ja"		=> "Japanese",								"kea_CV"	=> "Kabuverdianu (Cape Verde)",
		"kea"		=> "Kabuverdianu",							"kab_DZ"	=> "Kabyle (Algeria)",
		"kab"		=> "Kabyle",								"kl_GL"		=> "Kalaallisut (Greenland)",
		"kl"		=> "Kalaallisut",							"kln_KE"	=> "Kalenjin (Kenya)",
		"kln"		=> "Kalenjin",								"kam_KE"	=> "Kamba (Kenya)",
		"kam"		=> "Kamba",									"kn_IN"		=> "Kannada (India)",
		"kn"		=> "Kannada",								"kk_Cyrl"	=> "Kazakh (Cyrillic)",
		"kk_Cyrl_KZ"=> "Kazakh (Cyrillic, Kazakhstan)",			"kk"		=> "Kazakh",
		"km_KH"		=> "Khmer (Cambodia)",						"km"		=> "Khmer",
		"ki_KE"		=> "Kikuyu (Kenya)",						"ki"		=> "Kikuyu",
		"rw_RW"		=> "Kinyarwanda (Rwanda)",					"rw"		=> "Kinyarwanda",
		"kok_IN"	=> "Konkani (India)",						"kok"		=> "Konkani",
		"ko_KR"		=> "Korean (South Korea)",					"ko"		=> "Korean",
		"khq_ML"	=> "Koyra Chiini (Mali)",					"khq"		=> "Koyra Chiini",
		"ses_ML"	=> "Koyraboro Senni (Mali)",				"ses"		=> "Koyraboro Senni",
		"lag_TZ"	=> "Langi (Tanzania)",						"lag"		=> "Langi",
		"lv_LV"		=> "Latvian (Latvia)",						"lv"		=> "Latvian",
		"lt_LT"		=> "Lithuanian (Lithuania)",				"lt"		=> "Lithuanian",
		"luo_KE"	=> "Luo (Kenya)",							"luo"		=> "Luo",
		"luy_KE"	=> "Luyia (Kenya)",							"luy"		=> "Luyia",
		"mk_MK"		=> "Macedonian (Macedonia)",				"mk"		=> "Macedonian",
		"jmc_TZ"	=> "Machame (Tanzania)",					"jmc"		=> "Machame",
		"kde_TZ"	=> "Makonde (Tanzania)",					"kde"		=> "Makonde",
		"mg_MG"		=> "Malagasy (Madagascar)",					"mg"		=> "Malagasy",
		"ms_BN"		=> "Malay (Brunei)",						"ms_MY"		=> "Malay (Malaysia)",
		"ms"		=> "Malay",									"ml_IN"		=> "Malayalam (India)",
		"ml"		=> "Malayalam",								"mt_MT"		=> "Maltese (Malta)",
		"mt"		=> "Maltese",								"gv_GB"		=> "Manx (United Kingdom)",
		"gv"		=> "Manx",									"mr_IN"		=> "Marathi (India)",
		"mr"		=> "Marathi",								"mas_KE"	=> "Masai (Kenya)",
		"mas_TZ"	=> "Masai (Tanzania)",						"mas"		=> "Masai",
		"mer_KE"	=> "Meru (Kenya)",							"mer"		=> "Meru",
		"mfe_MU"	=> "Morisyen (Mauritius)",					"mfe"		=> "Morisyen",
		"naq_NA"	=> "Nama (Namibia)",						"naq"		=> "Nama",
		"ne_IN"		=> "Nepali (India)",						"ne_NP"		=> "Nepali (Nepal)",
		"ne"		=> "Nepali",								"nd_ZW"		=> "North Ndebele (Zimbabwe)",
		"nd"		=> "North Ndebele",							"nb_NO"		=> "Norwegian Bokmål (Norway)",
		"nb"		=> "Norwegian Bokmål",						"nn_NO"		=> "Norwegian Nynorsk (Norway)",
		"nn"		=> "Norwegian Nynorsk",						"nyn_UG"	=> "Nyankole (Uganda)",
		"nyn"		=> "Nyankole",								"or_IN"		=> "Oriya (India)",
		"or"		=> "Oriya",									"om_ET"		=> "Oromo (Ethiopia)",
		"om_KE"		=> "Oromo (Kenya)",							"om"		=> "Oromo",
		"ps_AF"		=> "Pashto (Afghanistan)",					"ps"		=> "Pashto",
		"fa_AF"		=> "Persian (Afghanistan)",					"fa_IR"		=> "Persian (Iran)",
		"fa"		=> "Persian",								"pl_PL"		=> "Polish (Poland)",
		"pl"		=> "Polish",								"pt_BR"		=> "Portuguese (Brazil)",
		"pt_GW"		=> "Portuguese (Guinea-Bissau)",			"pt_MZ"		=> "Portuguese (Mozambique)",
		"pt_PT"		=> "Portuguese (Portugal)",					"pt"		=> "Portuguese",
		"pa_Arab"	=> "Punjabi (Arabic)",						"pa_Arab_PK"=> "Punjabi (Arabic, Pakistan)",
		"pa_Guru"	=> "Punjabi (Gurmukhi)",					"pa_Guru_IN"=> "Punjabi (Gurmukhi, India)",
		"pa"		=> "Punjabi",								"ro_MD"		=> "Romanian (Moldova)",
		"ro_RO"		=> "Romanian (Romania)",					"ro"		=> "Romanian",
		"rm_CH"		=> "Romansh (Switzerland)",					"rm"		=> "Romansh",
		"rof_TZ"	=> "Rombo (Tanzania)",						"rof"		=> "Rombo",
		"ru_MD"		=> "Russian (Moldova)",						"ru_RU"		=> "Russian (Russia)",
		"ru_UA"		=> "Russian (Ukraine)",						"ru"		=> "Russian",
		"rwk_TZ"	=> "Rwa (Tanzania)",						"rwk"		=> "Rwa",
		"saq_KE"	=> "Samburu (Kenya)",						"saq"		=> "Samburu",
		"sg_CF"		=> "Sango (Central African Republic)",		"sg"		=> "Sango",
		"seh_MZ"	=> "Sena (Mozambique)",						"seh"		=> "Sena",
		"sr_Cyrl"	=> "Serbian (Cyrillic)",					"sr_Cyrl_BA"=> "Serbian (Cyrillic, Bosnia and Herzegovina)",
		"sr_Cyrl_ME"=> "Serbian (Cyrillic, Montenegro)",		"sr_Cyrl_RS"=> "Serbian (Cyrillic, Serbia)",
		"sr_Latn"	=> "Serbian (Latin)",						"sr_Latn_BA"=> "Serbian (Latin, Bosnia and Herzegovina)",
		"sr_Latn_ME"=> "Serbian (Latin, Montenegro)",			"sr_Latn_RS"=> "Serbian (Latin, Serbia)",
		"sr"		=> "Serbian",								"sn_ZW"		=> "Shona (Zimbabwe)",
		"sn"		=> "Shona",									"ii_CN"		=> "Sichuan Yi (China)",
		"ii"		=> "Sichuan Yi",							"si_LK"		=> "Sinhala (Sri Lanka)",
		"si"		=> "Sinhala",								"sk_SK"		=> "Slovak (Slovakia)",
		"sk"		=> "Slovak",								"sl_SI"		=> "Slovenian (Slovenia)",
		"sl"		=> "Slovenian",								"xog_UG"	=> "Soga (Uganda)",
		"xog"		=> "Soga",									"so_DJ"		=> "Somali (Djibouti)",
		"so_ET"		=> "Somali (Ethiopia)",						"so_KE"		=> "Somali (Kenya)",
		"so_SO"		=> "Somali (Somalia)",						"so"		=> "Somali",
		"es_AR"		=> "Spanish (Argentina)",					"es_BO"		=> "Spanish (Bolivia)",
		"es_CL"		=> "Spanish (Chile)",						"es_CO"		=> "Spanish (Colombia)",
		"es_CR"		=> "Spanish (Costa Rica)",					"es_DO"		=> "Spanish (Dominican Republic)",
		"es_EC"		=> "Spanish (Ecuador)",						"es_SV"		=> "Spanish (El Salvador)",
		"es_GQ"		=> "Spanish (Equatorial Guinea)",			"es_GT"		=> "Spanish (Guatemala)",
		"es_HN"		=> "Spanish (Honduras)",					"es_419"	=> "Spanish (Latin America)",
		"es_MX"		=> "Spanish (Mexico)",						"es_NI"		=> "Spanish (Nicaragua)",
		"es_PA"		=> "Spanish (Panama)",						"es_PY"		=> "Spanish (Paraguay)",
		"es_PE"		=> "Spanish (Peru)",						"es_PR"		=> "Spanish (Puerto Rico)",
		"es_ES"		=> "Spanish (Spain)",						"es_US"		=> "Spanish (United States)",
		"es_UY"		=> "Spanish (Uruguay)",						"es_VE"		=> "Spanish (Venezuela)",
		"es"		=> "Spanish",								"sw_KE"		=> "Swahili (Kenya)",
		"sw_TZ"		=> "Swahili (Tanzania)",					"sw"		=> "Swahili",
		"sv_FI"		=> "Swedish (Finland)",						"sv_SE"		=> "Swedish (Sweden)",
		"sv"		=> "Swedish",								"gsw_CH"	=> "Swiss German (Switzerland)",
		"gsw"		=> "Swiss German",							"shi_Latn"	=> "Tachelhit (Latin)",
		"shi_Latn_MA"=> "Tachelhit (Latin, Morocco)",			"shi_Tfng"	=> "Tachelhit (Tifinagh)",
		"shi_Tfng_MA"=> "Tachelhit (Tifinagh, Morocco)",		"shi"		=> "Tachelhit",
		"dav_KE"	=> "Taita (Kenya)",							"dav"		=> "Taita",
		"ta_IN"		=> "Tamil (India)",							"ta_LK"		=> "Tamil (Sri Lanka)",
		"ta"		=> "Tamil",									"te_IN"		=> "Telugu (India)",
		"te"		=> "Telugu",								"teo_KE"	=> "Teso (Kenya)",
		"teo_UG"	=> "Teso (Uganda)",							"teo"		=> "Teso",
		"th_TH"		=> "Thai (Thailand)",						"th"		=> "Thai",
		"bo_CN"		=> "Tibetan (China)",						"bo_IN"		=> "Tibetan (India)",
		"bo"		=> "Tibetan",								"ti_ER"		=> "Tigrinya (Eritrea)",
		"ti_ET"		=> "Tigrinya (Ethiopia)",					"ti"		=> "Tigrinya",
		"to_TO"		=> "Tonga (Tonga)",							"to"		=> "Tonga",
		"tr_TR"		=> "Turkish (Turkey)",						"tr"		=> "Turkish",
		"uk_UA"		=> "Ukrainian (Ukraine)",					"uk"		=> "Ukrainian",
		"ur_IN"		=> "Urdu (India)",							"ur_PK"		=> "Urdu (Pakistan)",
		"ur"		=> "Urdu",									"uz_Arab"	=> "Uzbek (Arabic)",
		"uz_Arab_AF"=> "Uzbek (Arabic, Afghanistan)",			"uz_Cyrl"	=> "Uzbek (Cyrillic)",
		"uz_Cyrl_UZ"=> "Uzbek (Cyrillic, Uzbekistan)",			"uz_Latn"	=> "Uzbek (Latin)",
		"uz_Latn_UZ"=> "Uzbek (Latin, Uzbekistan)",				"uz"		=> "Uzbek",
		"vi_VN"		=> "Vietnamese (Vietnam)",					"vi"		=> "Vietnamese",
		"vun_TZ"	=> "Vunjo (Tanzania)",						"vun"		=> "Vunjo",
		"cy_GB"		=> "Welsh (United Kingdom)",				"cy"		=> "Welsh",
		"yo_NG"		=> "Yoruba (Nigeria)",						"yo"		=> "Yoruba",
		"zu_ZA"		=> "Zulu (South Africa)",					"zu"		=> "Zulu"
	];

	/**
	 * Return localization code, language and locale code separated by underscore.
	 * @return string
	 */
	public function GetValue () {
		return $this->value;
	}

	/**
	 * Set localization value (language and country code separated by underscore). 
	 * If there is given localization code separated by dash, it's automatically
	 * replaced by underscore.
	 * @param  string $localizationCode 
	 * @return \MvcCore\Ext\Forms\Fields\LocalizationSelect
	 */
	public function SetValue ($localizationCode) {
		/** @var \MvcCore\Ext\Forms\Fields\LocalizationSelect $this */
		$this->value = static::normalizeLocalizationCode($localizationCode);
		return $this;
	}

	/**
	 * Get all existing localization codes as array with keys as localization 
	 * codes and values as not translated English localization names.
	 * @return array
	 */
	public static function & GetAllOptions () {
		/** @var \MvcCore\Ext\Forms\Fields\LocalizationSelect $this */
		return static::$allOptions;
	}

	/**
	 * Set all existing localization codes as array with keys as localization 
	 * codes and values as not translated English localization names.
	 * Given value will be automatically used as select options, if there 
	 * will not be configured any filtering to filter displayed localizations.
	 * @param array $allOptions 
	 */
	public static function SetAllOptions ($allOptions = []) {
		/** @var \MvcCore\Ext\Forms\Fields\LocalizationSelect $this */
		static::$allOptions = $allOptions;
	}

	/**
	 * Filter displayed localizations to not show every time all existing 
	 * localizations in the world. Given localizations codes will be automatically
	 * converted to upper case.
	 * @param  \string[] $localizationsCodes Array of localization codes strings to rendered only, not to render all existing localizations.
	 * @return \MvcCore\Ext\Forms\Fields\LocalizationSelect
	 */
	public function FilterOptions ($localizationsCodes = []) {
		/** @var \MvcCore\Ext\Forms\Fields\LocalizationSelect $this */
		$options = [];
		foreach ($localizationsCodes as $localizationCode) {
			$localizationCode = static::normalizeLocalizationCode($localizationCode);
			if (isset(static::$allOptions[$localizationCode])) {
				$options[$localizationCode] = static::$allOptions[$localizationCode];
			} else {
				$options[$localizationCode] = $localizationCode;
			}
		}
		$this->options = & $options;
		return $this;
	}
	
	/**
	 * Create new form localization `<select>` control instance.
	 * If there is record under `filter` key in `$cfg` array argument,
	 * it's used for method $field->FilterOptions([...]);` method.
	 * 
	 * @param  array                          $cfg
	 * Config array with public properties and it's
	 * values which you want to configure, presented
	 * in camel case properties names syntax.
	 * 
	 * @param  string                         $name 
	 * Form field specific name, used to identify submitted value.
	 * This value is required for all form fields.
	 * @param  string           $type 
	 * Fixed field order number, null by default.
	 * @param  int                            $fieldOrder
	 * Form field type, used in `<input type="...">` attribute value.
	 * Every typed field has it's own string value, but base field type 
	 * `\MvcCore\Ext\Forms\Field` has `NULL`.
	 * @param  string|\string[]               $value 
	 * Form field value. It could be string or array, int or float, it depends
	 * on field implementation. Default value is `NULL`.
	 * @param  string                         $title 
	 * Field title, global HTML attribute, optional.
	 * @param  string                         $translate 
	 * Boolean flag about field visible texts and error messages translation.
	 * This flag is automatically assigned from `$field->form->GetTranslate();` 
	 * flag in `$field->Init();` method.
	 * @param  string                         $translateTitle 
	 * Boolean to translate title text, `TRUE` by default.
	 * @param  array                          $cssClasses 
	 * Form field HTML element css classes strings.
	 * Default value is an empty array to not render HTML `class` attribute.
	 * @param  array                          $controlAttrs 
	 * Collection with field HTML element additional attributes by array keys/values.
	 * Do not use system attributes as: `id`, `name`, `value`, `readonly`, `disabled`, `class` ...
	 * Those attributes has it's own configurable properties by setter methods or by constructor config array.
	 * HTML field elements are meant: `<input>, <button>, <select>, <textarea> ...`. 
	 * Default value is an empty array to not render any additional attributes.
	 * @param  array                          $validators 
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
	 * @param  string                         $accessKey
	 * The access key global attribute provides a hint for generating
	 * a keyboard shortcut for the current element. The attribute 
	 * value must consist of a single printable character (which 
	 * includes accented and other characters that can be generated 
	 * by the keyboard).
	 * @param  bool                           $autoFocus
	 * This Boolean attribute lets you specify that a form control should have input
	 * focus when the page loads. Only one form-associated element in a document can
	 * have this attribute specified. 
	 * @param  bool                           $disabled
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
	 * @param  bool                           $readOnly
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
	 * @param  bool                           $required
	 * Form field attribute `required`, determination
	 * if control will be required to complete any value by user.
	 * This flag is also used for submit checking. Default value is `NULL`
	 * to not require any field value. If form has configured it's property
	 * `$form->GetDefaultRequired()` to `TRUE` and this value is `NULL`, field
	 * will be automatically required by default form configuration.
	 * @param  int|string                     $tabIndex
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
	 * @param  string                         $label
	 * Control label visible text. If field form has configured any translator, translation 
	 * will be processed automatically before rendering process. Default value is `NULL`.
	 * @param  bool                           $translateLabel
	 * Boolean to translate label text, `TRUE` by default.
	 * @param  string                         $labelSide
	 * Label side from rendered field - location where label will be rendered.
	 * By default `$this->labelSide` is configured to `left`.
	 * If you want to reconfigure it to different side,
	 * the only possible value is `right`.
	 * You can use constants:
	 * - `\MvcCore\Ext\Forms\IField::LABEL_SIDE_LEFT`
	 * - `\MvcCore\Ext\Forms\IField::LABEL_SIDE_RIGHT`
	 * @param  int                            $renderMode
	 * Rendering mode flag how to render field and it's label.
	 * Default value is `normal` to render label and field, label 
	 * first or field first by another property `$field->labelSide = 'left' | 'right';`.
	 * But if you want to render label around field or if you don't want
	 * to render any label, you can change this with constants (values):
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_NORMAL`       - `<label /><input />`
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_LABEL_AROUND` - `<label><input /></label>`
	 * - `\MvcCore\Ext\Form::FIELD_RENDER_MODE_NO_LABEL`     - `<input />`
	 * @param  array                          $labelAttrs
	 * Collection with `<label>` HTML element additional attributes by array keys/values.
	 * Do not use system attributes as: `id`,`for` or `class`, those attributes has it's own 
	 * configurable properties by setter methods or by constructor config array. Label `class` 
	 * attribute has always the same css classes as it's field automatically. 
	 * Default value is an empty array to not render any additional attributes.
	 * 
	 * @param  string                         $autoComplete 
	 * Attribute indicates if the input can be automatically completed 
	 * by the browser, usually by remembering previous values the user 
	 * has entered. Possible values: `off`, `on`, `name`, `email`, 
	 * `username`, `country`, `postal-code` and many more...
	 * 
	 * @param  bool                           $multiple
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
	 * @param  array                          $options
	 * Form group control options to render more sub-control attributes for specified
	 * submitted values (array keys). This property configuration is required.
	 * @param  bool                           $translateOptions
	 * Boolean about to translate options texts, default `TRUE` to translate.
	 * @param  callable|\Closure|array|string $optionsLoader
	 * Callable or dynamic callable definition to load control options.
	 * Value could be:
	 * - Standard PHP callable or `\Closure` function.
	 * - Dynamic callable definition by array with first item to define context
	 *   definition int flag, where the method (second array item) is located, 
	 *   you can use constants:
	 *   - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_FORM`
	 *   - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_FORM_STATIC`
	 *   - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_CTRL`
	 *   - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_CTRL_STATIC`
	 *   - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_MODEL`
	 *   - `\MvcCore\Ext\Forms\Fields\IOptions::LOADER_CONTEXT_MODEL_STATIC`
	 *   Last two constants are usefull only for `mvccore/ext-model-form` extension.
	 * 
	 * @param  int                            $minOptions
	 * Minimum options count to select. 
	 * Default value is `NULL` to not limit anything.
	 * @param  int                            $maxOptions
	 * Maximum options count to select. 
	 * Default value is `NULL` to not limit anything.
	 * 
	 * @param  string                         $nullOptionText 
	 * This attribute is a text placeholder for `<select>` controls,
	 * when no option is selected yet. Typically: `--- please select an option ---`.
	 * It's rendered every time this placeholder has any text value, doesn't matter,
	 * if `<select>` tag has already any value selected or not yet . It's rendered usually 
	 * as first `<option>` sub-element with an empty value, as `disabled` and `selected` 
	 * `<option>` tag. `NULL` value means no placeholder `<option>` tag will be rendered.
	 * @param  bool                           $translateNullOptionText
	 * Boolean to translate placeholder text, `TRUE` by default.
	 * 
	 * @param  int                            $size
	 * If the field is `<input>`, this attribute is initial size of the control. Starting in HTML5, 
	 * this attribute applies only when the `type` attribute is set to `text`, `search`, `tel`, `url`, 
	 * `email`, or `password`, otherwise it is ignored. The `size` must be an integer greater than zero. 
	 * The default browser`s value is 20.
	 * If the field is `<select>`, this attribute is presented as a scrolling list box (e.g. when 
	 * `multiple` attribute is specified to `TRUE`), this attribute represents the number of rows in 
	 * the list that should be visible at one time. Browsers are not required to present a select element 
	 * as a scrolled list box. The default browser`s value is `0`.
	 * 
	 * @param  string                         $wrapper
	 * Html code wrapper, wrapper has to contain replacement in string 
	 * form: `{control}`. Around this substring you can wrap any HTML 
	 * code you want. Default wrapper values is: `'{control}'`.
	 * 
	 * @param  array                          $filter
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
		$optionsLoader = [],
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
	 * @return \MvcCore\Ext\Forms\Fields\LocalizationSelect
	 */
	public function SetForm (\MvcCore\Ext\IForm $form) {
		/** @var \MvcCore\Ext\Forms\Fields\LocalizationSelect $this */
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
		/** @var \MvcCore\Ext\Forms\Fields\LocalizationSelect $this */
		$result = '';
		$valueTypeIsArray = is_array($this->value);
		if ($this->nullOptionText !== NULL && strlen((string) $this->nullOptionText) > 0) {
			// advanced configuration with key, text, CSS class, and any other attributes for single option tag
			$result .= $this->renderControlOptionsAdvanced(
				'', [
					'value'	=> '',
					'text'	=> htmlspecialchars_decode(htmlspecialchars($this->nullOptionText, ENT_QUOTES), ENT_QUOTES),
					'class'	=> 'localization-none',
					//'attrs'	=> ['disabled' => 'disabled']
				], $valueTypeIsArray
			);
		}
		foreach ($this->options as $key => $value) {
			// advanced configuration with key, text, CSS class, and any other attributes for single option tag
			$result .= $this->renderControlOptionsAdvanced($key, [
				'class'	=> 'localization-' . str_replace('_', '-', strtolower($key)),
				'text'	=> htmlspecialchars_decode(htmlspecialchars($value, ENT_QUOTES), ENT_QUOTES),
				'value'	=> htmlspecialchars_decode(htmlspecialchars($key, ENT_QUOTES), ENT_QUOTES),
			], $valueTypeIsArray);
		}
		return $result;
	}

	/**
	 * Normalize localization code into the right form with underscore separator:
	 * - lower language code
	 * - (optional alphabetical code in pascal case)
	 * - upper case country code
	 * @param  string|NULL $rawLocalizationCode 
	 * @return string|NULL
	 */
	protected static function normalizeLocalizationCode ($rawLocalizationCode) {
		/** @var \MvcCore\Ext\Forms\Fields\LocalizationSelect $this */
		if ($rawLocalizationCode === NULL) return NULL;
		$explodedValues = explode('_', str_replace('-', '_', $rawLocalizationCode));
		$explodedValuesCount = count($explodedValues);
		if ($explodedValuesCount === 3) 
			$explodedValues[1] = ucfirst(strtolower($explodedValues[1]));
		$explodedValues[0] = strtolower($explodedValues[0]);
		$lastItemIndex = $explodedValuesCount - 1;
		$explodedValues[$lastItemIndex] = mb_strtoupper($explodedValues[$lastItemIndex]);
		return implode('_', $explodedValues);
	}
}
