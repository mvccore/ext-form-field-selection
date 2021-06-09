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

namespace MvcCore\Ext\Forms\Validators;

/**
 * Responsibility: Validate hexadecimal color with no transparency including 
 *                 leading hash char `#`.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Color extends \MvcCore\Ext\Forms\Validator {

	/**
	 * Error message index(es).
	 * @var int
	 */
	const ERROR_COLOR = 3;

	/**
	 * Validation failure message template definitions.
	 * @var array
	 */
	protected static $errorMessages = [
		self::ERROR_COLOR	=> "Field '{0}' requires a valid color in hexadecimal format '#[0-9A-F]{6}'.",
	];

	/**
	 * Validate fully opaque color string by PHP `preg_match('/^#[0-9A-F]{6}$/', strtoupper($rawSubmittedValue));`.
	 * @param  string|array $rawSubmittedValue Raw submitted value from user.
	 * @return string|NULL  Safe submitted value or `NULL` if not possible to return safe value.
	 */
	public function Validate ($rawSubmittedValue) {
		$result = strtoupper(trim((string) $rawSubmittedValue));
		if (mb_strlen($result) === 0) return NULL;
		$matched = preg_match('/^#[0-9A-F]{6}$/', $result);
		if (!$matched) {
			$this->field->AddValidationError(
				static::GetErrorMessage(self::ERROR_COLOR)
			);
			$result = NULL;
		}
		return $result;
	}
}
