<?php declare(strict_types=1);

namespace Starburst\Utils;

final class Validators
{
	/**
	 * Checks if the value is a valid UTF-8 string.
	 */
	public static function isUnicode(mixed $value): bool
	{
		return is_string($value) && preg_match('##u', $value);
	}

	/**
	 * Checks if value is a valid kennitala
	 */
	public static function isKennitala(mixed $value): bool
	{
		if (!is_numeric($value)) {
			return false;
		}
		$value = (string)preg_replace('#(\d{6})-?(\d{4})#', '\1\2', (string)$value);

		if (strlen($value) !== 10) {
			return false;
		}

		$sum = (int)($value[0]) * 3;
		$sum += (int)($value[1]) * 2;
		$sum += (int)($value[2]) * 7;
		$sum += (int)($value[3]) * 6;
		$sum += (int)($value[4]) * 5;
		$sum += (int)($value[5]) * 4;
		$sum += (int)($value[6]) * 3;
		$sum += (int)($value[7]) * 2;

		$checksum = 11 - ($sum % 11);
		if ($checksum === 11) {
			$checksum = 0;
		}

		return ($checksum === (int)$value[8]);
	}

	/**
	 * Checks if the value is a valid email address.
	 * It does not verify that the domain actually exists, only the syntax is verified.
	 */
	public static function isEmail(string $value): bool
	{
		// we can't use FILTER_EMAIL because it doesn't support emails with icelandic chars
		$atom = "[-a-z0-9!#$%&'*+/=?^_`{|}~]"; // RFC 5322 unquoted characters in local-part
		$alpha = "a-z\x80-\xFF"; // superset of IDN
		return (bool) preg_match(<<<XX
			(^(?n)
				("([ !#-[\\]-~]*|\\\\[ -~])+"|$atom+(\\.$atom+)*)  # quoted or unquoted
				@
				([0-9$alpha]([-0-9$alpha]{0,61}[0-9$alpha])?\\.)+  # domain - RFC 1034
				[$alpha]([-0-9$alpha]{0,17}[$alpha])?              # top domain
			$)Dix
			XX, $value);
	}


	/**
	 * Checks if the value is a valid phone number based on the input locale
	 */
	public static function isPhoneNumber(string $value, string $locale = 'is'): bool
	{
		if ($locale === 'is') {
			return preg_match('#^\d{7}$#', $value) || preg_match('#^\+\d{9,}$#', $value);
		}
		return false;
	}
}
