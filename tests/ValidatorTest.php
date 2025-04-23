<?php declare(strict_types=1);

namespace Starburst\Utils\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Starburst\Utils\Validators;

final class ValidatorTest extends TestCase
{
	/**
	 * @return list<array{mixed, bool}>
	 */
	public static function dataProviderForIsUnicode(): array
	{
		return [
			['a', true],
			["\x8F", false],
			[10, false],
			['Test Unicode string 💻', true],
		];
	}

	#[DataProvider('dataProviderForIsUnicode')]
	public function testIsUnicode(mixed $value, bool $expectedResult): void
	{
		$this->assertSame($expectedResult, Validators::isUnicode($value));
	}

	public function testValidKennitala(): void
	{
		$validKennitala = '5209032750';
		$this->assertTrue(
			Validators::isKennitala($validKennitala),
			"{$validKennitala} should be a valid kennitala",
		);
	}

	public function testKennitalaWithInValidChecksum(): void
	{
		$validKennitala = '5209132751';
		$this->assertFalse(
			Validators::isKennitala($validKennitala),
			"{$validKennitala} should have invalid checksum",
		);
	}

	public function testInvalidKennitala(): void
	{
		$invalidKennitala = 'abcd20393939';
		$this->assertFalse(
			Validators::isKennitala($invalidKennitala),
			"{$invalidKennitala} is an invalid kennitala",
		);
	}

	public function testKennitalaWithWrongFormat(): void
	{
		$wrongFormatKennitala = '120170';
		$this->assertFalse(
			Validators::isKennitala($wrongFormatKennitala),
			"{$wrongFormatKennitala} is a wrongly formatted kennitala",
		);
	}

	/**
	 * @return array<string, array{0: string}>
	 */
	public static function validPhoneNumbers(): array
	{
		return [
			'simple' => ['1234567'],
			'with country prefix' => ['+3541234567'],
		];
	}

	/**
	 * @return array<string, array{0: string}>
	 */
	public static function invalidPhoneNumbers(): array
	{
		return [
			'to short' => ['123456'],
			'to long' => ['123456123456'],
			'contains letter' => ['123456U'],
			'invalid country code' => ['+4612345671'],
		];
	}

	#[DataProvider('validPhoneNumbers')]
	public function testValidPhoneNumber(string $phoneNumber): void
	{
		$this->assertTrue(Validators::isIcelandicPhoneNumber($phoneNumber));
	}

	#[DataProvider('invalidPhoneNumbers')]
	public function testInvalidPhoneNumber(string $phoneNumber): void
	{
		$this->assertFalse(Validators::isIcelandicPhoneNumber($phoneNumber));
	}

	/**
	 * @return list<array{string, bool}>
	 */
	public static function emailDataProvider(): array
	{
		return [
			['validemail@example.com', true],
			['invalidemail.@example.com', false],
			['@.com', false],
			['valid-letter@example.com', true],
			['validemail@.com', false],
			['valid.email@example.com', true],
		];
	}

	#[DataProvider('emailDataProvider')]
	public function testEmail(string $email, bool $expected): void
	{
		$result = Validators::isEmail($email);
		$this->assertSame($expected, $result);
	}
}
