<?php declare(strict_types=1);

namespace Starburst\Utils;

final class Text
{
	private const LOCALE_TO_TRANSLITERATOR_ID = [
		'de' => 'de-ASCII',
		'is' => '', // covered by latin-ASCII
		'en' => '', // covered by latin-ASCII
		'ru' => 'Russian-Latin',
		'uk' => 'Ukrainian-Latin',
	];

	/** @var array<string, \Transliterator> */
	private static array $transliterators = [];

	/**
	 * Removes control characters, normalizes line breaks to `\n`, removes leading and trailing blank lines,
	 * trims end spaces on lines, normalizes UTF-8 to the normal form of NFC.
	 */
	public static function normalize(string $string): string
	{
		// convert to compressed normal form (NFC)
		/** @var false|string $n */
		$n = \Normalizer::normalize($string, \Normalizer::FORM_C);
		if ($n !== false) {
			$string = $n;
		}

		$string = (string)preg_replace("~\r\n?|\u{2028}|\u{2029}~", "\n", $string);

		// remove control characters; leave \t + \n
		$string = (string)preg_replace('#[\x00-\x08\x0B-\x1F\x7F-\x9F]+#u', '', $string);

		// right trim
		$string = (string)preg_replace('#[\t ]+$#m', '', $string);

		// leading and trailing blank lines
		return trim($string, "\n");
	}

	/**
	 * Truncates a UTF-8 string to given maximal length, while trying not to split whole words.
	 * Only if the string is truncated, an ellipsis (or something else set with third argument)
	 * is appended to the string.
	 */
	public static function truncate(string $string, int $maxLen, string $append = "\u{2026}"): string
	{
		if (mb_strlen($string) <= $maxLen) {
			return $string;
		}
		$maxLen -= mb_strlen($append);
		if ($maxLen < 1) {
			return $append;
		}

		if (preg_match('#^.{1,' . $maxLen . '}(?=[\s\x00-/:-@\[-`{-~])#us', $string, $matches)) {
			return $matches[0] . $append;
		}

		return mb_substr($string, 0, $maxLen) . $append;
	}

	public static function slugify(
		string $string,
		string $separator = '-',
		bool $allowPeriod = false,
		string $locale = 'is',
	): string {
		if (!isset(self::LOCALE_TO_TRANSLITERATOR_ID[$locale])) {
			throw new \InvalidArgumentException(sprintf(
				'Invalid locale. Only "%s" allowed',
				implode(', ', array_keys(self::LOCALE_TO_TRANSLITERATOR_ID)),
			));
		}

		$period = $allowPeriod ? '.' : '';
		$localeKey = $locale . $period;
		if (!isset(self::$transliterators[$localeKey])) {
			$rules = [
				':: latin-ASCII;',
				':: Any-Latin;',
				':: NFD;',
				':: [:Nonspacing Mark:] Remove;',
				':: NFC;',
				':: [^-' . $period . '[:^Punctuation:]] Remove;',
				':: Lower();',
				'[:^L:] { [-] > ;',
				'[-] } [:^L:] > ;',
				"[-[:Separator:]]+ > '" . $separator . "';",
			];
			if (self::LOCALE_TO_TRANSLITERATOR_ID[$locale]) {
				array_unshift($rules, ':: ' . self::LOCALE_TO_TRANSLITERATOR_ID[$locale] . ';');
			}

			$transliterator = \Transliterator::createFromRules(implode('', $rules));
			if (!$transliterator) {
				throw new \BadMethodCallException('Failed to create transliterator');
			}
			self::$transliterators[$localeKey] = $transliterator;
		}

		$slug = self::$transliterators[$localeKey]->transliterate($string);
		if (!$slug) {
			throw new \BadMethodCallException(sprintf('Unable to transliterate string: %s', $string));
		}

		return $slug;
	}

	/**
	 * Normalizes a phone number to the format +3541234567
	 *
	 * It will add the country code if it's missing
	 */
	public static function normalizeIcelandicPhoneNumber(string $phoneNumber): string
	{
		$phoneNumber = (string)preg_replace('/[^+0-9]/', '', $phoneNumber);
		if (!preg_match('/^(\+354)?\d{7}$/', $phoneNumber, $matches)) {
			throw new \InvalidArgumentException('Does not look like an Icelandic phone number');
		}
		if (!isset($matches[1])) {
			$phoneNumber = '+354' . $phoneNumber;
		}

		return $phoneNumber;
	}
}
