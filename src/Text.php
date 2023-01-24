<?php declare(strict_types=1);

namespace Starburst\Utils;

final class Text
{

	/**
	 * Removes control characters, normalizes line breaks to `\n`, removes leading and trailing blank lines,
	 * trims end spaces on lines, normalizes UTF-8 to the normal form of NFC.
	 */
	public static function normalize(string $string): string
	{
		// convert to compressed normal form (NFC)
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
}
