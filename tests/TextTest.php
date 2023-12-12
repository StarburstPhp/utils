<?php declare(strict_types=1);

namespace Starburst\Utils\Tests;

use PHPUnit\Framework\TestCase;
use Starburst\Utils\Text;

final class TextTest extends TestCase
{
	public function testTruncate(): void
	{
		// Test string shorter than max length.
		$string = "short";
		$result = Text::truncate($string, 10);
		$this->assertEquals(
			$string,
			$result,
			'The truncated string should be same as original when less than max length',
		);

		// Test string exactly the max length.
		$string = "just enough";
		$result = Text::truncate($string, 11);
		$this->assertEquals(
			$string,
			$result,
			'The truncated string should be same as original when exactly max length',
		);

		// Test string longer than max length.
		$string = "this string is too long for the limit";
		$result = Text::truncate($string, 20);
		// Expecting: "this string is too..."
		$expected = "this string is too\u{2026}";
		$this->assertEquals(
			$expected,
			$result,
			'The truncated string should cut at word boundary and append ellipsis when longer than max length',
		);

		// Test string where we can't respect word boundary due to short limit.
		$string = "supercalifragilistic";
		$result = Text::truncate($string, 10);
		// Expecting: "supercali..."
		$expected = "supercali\u{2026}";
		$this->assertEquals(
			$expected,
			$result,
			'The truncated string should cut within word and append ellipsis when it can\'t respect word boundary',
		);

		// Test string truncation with max length less than append string.
		$string = "any string";
		$result = Text::truncate($string, 5);
		$this->assertEquals(
			"any\u{2026}",
			$result,
			'The truncated string should be solely the append string when max length is less than append string length',
		);
	}

	public function testSlugifyWithValidInput(): void
	{
		$this->assertEquals('abc-def', Text::slugify('abc def'));
		$this->assertEquals('abc-def-ghi', Text::slugify('abc def ghi'));
		$this->assertEquals('abc', Text::slugify('abc'));
	}

	public function testSlugifyWithInvalidLocale(): void
	{
		$this->expectException(\InvalidArgumentException::class);
		Text::slugify('abc def ghi', '-', true, 'xyz');
	}

	public function testSlugifyWithTransliterationFailure(): void
	{
		$this->expectException(\BadMethodCallException::class);
		Text::slugify('abc def ghi', '-', true, 'ru');
	}

	public function testSlugifyAllowsPeriodWhenSet(): void
	{
		$this->assertEquals('abcd.efg', Text::slugify('abcd.efg', '-', true, 'en'));
		$this->assertEquals('abcdef', Text::slugify('abc.def', '-', false, 'en'));
	}

	public function testSlugifyWithNonAsciiCharacters(): void
	{
		$this->assertEquals('i-c-e', Text::slugify('í c é', '-', false, 'is'));
		$this->assertEquals('oe-ae-toe', Text::slugify('ö ä tö', '-', false, 'de'));
		$this->assertEquals('hello-world', Text::slugify('héllo wôrld', '-', false, 'en'));
	}

	public function testNormalize(): void
	{
		// Use case 1: Testing with ASCII characters
		$string = "    Hello\n  World\r  \t";
		$expected = "    Hello\n  World";
		$this->assertEquals($expected, Text::normalize($string));

		// Use case 2: Testing with non-ASCII characters
		$string = "  Héllo\r\nWörlδ   ";
		$expected = "  Héllo\nWörlδ";
		$this->assertEquals($expected, Text::normalize($string));

		// Use case 3: Testing line breaks normalization
		$string = "Line\r\nbreaks\nshould\rbe\n\nnormalized";
		$expected = "Line\nbreaks\nshould\nbe\n\nnormalized";
		$this->assertEquals($expected, Text::normalize($string));
	}
}
