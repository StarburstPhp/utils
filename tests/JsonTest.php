<?php declare(strict_types=1);

namespace Starburst\Utils\Tests;

use JsonException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Starburst\Utils\Json;

final class JsonTest extends TestCase
{
	public function testEncode(): void
	{
		$expectedResult = '{"name":"John Doe","email":"john.doe@example.com"}';
		$jsonResult = Json::encode(['name' => 'John Doe', 'email' => 'john.doe@example.com']);

		$this->assertIsString($jsonResult);
		$this->assertEquals($expectedResult, $jsonResult);
	}

	public function testEncodeFails(): void
	{
		$value = array_fill(0, 1025, 'depth');
		for ($i = 0; $i < 1024; $i++) {
			$value = [$value];
		}

		$this->expectException(JsonException::class);

		Json::encode($value);
	}

	public function testDecodeMethod(): void
	{
		$valueToDecode = '{"name":"John","age":30,"city":"New York"}';
		$expectedResult = ['name' => 'John', 'age' => 30, 'city' => 'New York'];

		$actualResult = Json::decode($valueToDecode);

		$this->assertEquals($expectedResult, $actualResult);
	}

	public function testDecodeMethodWithIncorrectInput(): void
	{
		$valueToDecode = 'incorrect_input';

		$this->expectException(JsonException::class);

		Json::decode($valueToDecode);
	}

	public function testDecodeArray(): void
	{
		$json = '{"key1":"value1", "key2":"value2"}';
		$expectedResult = ['key1' => 'value1', 'key2' => 'value2'];

		$result = Json::decodeArray($json);

		$this->assertSame($expectedResult, $result);
	}

	public function testDecodeArrayWithInvalidInput(): void
	{
		$this->expectException(JsonException::class);
		$this->expectExceptionMessage('Output is not an array');

		$json = '"not a valid json array"';
		Json::decodeArray($json);
	}

	public function testDecodeArrayWithEmptyInput(): void
	{
		$json = '{}';
		$expectedResult = [];

		$result = Json::decodeArray($json);

		$this->assertSame($expectedResult, $result);
	}

	/**
	 * @param array<mixed> $expectedList
	 */
	#[DataProvider('jsonListProvider')]
	public function testDecodeList(string $json, array $expectedList): void
	{
		$this->assertSame($expectedList, Json::decodeList($json));
	}

	public function testDecodeListException(): void
	{
		$this->expectException(JsonException::class);
		$this->expectExceptionMessage('Output is not an list');
		Json::decodeList('{"a":"b"}');
	}

	/**
	 * @return array<string, array{string, array<mixed>}>
	 */
	public static function jsonListProvider(): array
	{
		return [
			'empty list' => ["[]", []],
			'single item' => ['["a"]', ['a']],
			'multiple items' => ['["a","b","c"]', ['a', 'b', 'c']],
		];
	}
}
