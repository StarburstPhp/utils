# Starburst Utils

[![Latest Version on Packagist](https://img.shields.io/packagist/v/starburst/utils.svg)](https://packagist.org/packages/starburst/utils)
[![Software License](https://img.shields.io/github/license/StarburstPhp/utils.svg)](LICENSE)

Package that contain some common helper classes

## Requirements

PHP 8.0 or higher.

## Installation

```bash
composer require starburst/utils
```

## Usage

### Json

The `Starburst\Utils\Json` class provides static methods for encoding and 
decoding Json in PHP.
Below, you'll find detailed documentation on how to use it:

#### `encode(mixed $value): string`

This method accepts a variable of any type and converts it to a JSON string.

**Parameters:**

* `$value (mixed)`: The value you want to encode to JSON.

**Returns:**

* JSON Encoded string

**Usage:**
```php
$jsonString = \Starburst\Utils\Json::encode(["name" => "John Doe", "age" => 30]);
```

#### `decode(string $value): mixed`

This method accepts a JSON string and decodes it to the corresponding PHP value or array.

**Parameters:**

* `$value (string)`: The JSON string you want to decode.

**Returns:**

* The PHP value that was encoded in the JSON string. If json is an object it's returned as an assoc array

**Usage:**
```php
$phpValue = \Starburst\Utils\Json::decode($jsonString);
```

#### `decodeArray(string $value): array`

This method decodes a JSON string to an associative array in PHP.

The method throws a JsonException if the decoded value is not an array.

**Parameters:**

* `$value (string)`: The JSON string you want to decode to an array.

**Returns:**

* The associative array that was encoded in the JSON string.

**Usage:**
```php
$associativeArray = \Starburst\Utils\Json::decodeArray($jsonString);
```

#### `decodeList(string $value): array`

This method decodes a JSON string to a numeric array (list) in PHP.

The method throws a JsonException if the decoded value is not a list.

**Parameters:**

* `$value (string)`: The JSON string you want to decode to an array.

**Returns:**

* The numeric array (list) that was encoded in the JSON string.

**Usage:**
```php
$list = \Starburst\Utils\Json::decodeList($jsonString);
```

### Validators

The `Starburst\Utils\Validators` class provides static methods to validate different types of input.

### `isUnicode(mixed $value): bool`

This function checks if the provided value is a valid UTF-8 string.

**Parameters:**

* `$value (mixed)`: The value you want to check.

**Returns:**

* True or false. It will always return false for none string types

**Usage:**

```php
$isValid = \Starburst\Utils\Validators::isUnicode($value);
```

#### `isKennitala(mixed $value): bool`

This function checks if the provided value is a valid kennitala.

**Parameters:**

* `$value (mixed)`: The value you want to check.

**Returns:**

* True or false

**Usage:**

```php
$isValid = \Starburst\Utils\Validators::isKennitala($value);
```

#### `isEmail(string $value): bool`

This function checks if the provided string is a valid email address. 
Note that it only checks the syntax of the email and not if the email domain actually exists.

**Parameters:**

* `$value (string)`: The value you want to check.

**Returns:**

* True or false

**Usage:**

```php
$isValid = \Starburst\Utils\Validators::isEmail($value);
```

### GetArrayCopy

Trait that helps convert an object into an assoc array. 

It supports value resolvers that can be used to format some properties in a custom way.

#### Example

```php
class TestObject
{
	use \Starburst\Utils\Traits\GetArrayCopyTrait;
	
	public function __construct(
		public int $id,
		#[\Starburst\Utils\Attributes\DateFormat('Y-m-d')]
		public \DateTimeImmutable $startDate,
		public \DateTimeImmutable $createdOn,
		#[\Starburst\Utils\Attributes\HiddenProperty]
		public string $internalField,
		#[\Starburst\Utils\Attributes\CustomName('parent')]
		public ?TestObject $parentObject = null,
	) {}
}

$obj = new TestObject(
	1,
	new DateTimeImmutable('2024-05-24 08:00:00'),
	new DateTimeImmutable('2024-05-20 12:23:01'),
	'internalValue'
);

$obj->getArrayCopy() === [
	'id' => 1,
	'startOn' => '2024-05-24',
	'createdOn' => '2024-05-20 12:23:01',
	'parent' => null,
];
```

#### Configure custom value resolvers

```php
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class CustomAttribute {}
class CustomResolver implements \Starburst\Utils\ValueResolvers\ValueResolver
{
	/**
	 * @param \WeakMap<object, mixed> $tracker
	 */
	public function resolve(mixed $value, \WeakMap $tracker, ?\ReflectionProperty $reflectionProperty = null): mixed
	{
		$attrs = $reflectionProperty?->getAttributes(CustomAttribute::class);
		if (!$attrs) {
			return $value;
		}
		if ($value instanceof \DateTimeInterface) {
			return $value->format('j.m k\l. H:i');
		}
		
		return 'Random string';
	}

}

$collection = new \Starburst\Utils\ValueResolvers\ResolverCollection(
	new \Starburst\Utils\Tests\Stubs\CustomValueResolver(),
);

$obj = new class (1) {
	use \Starburst\Utils\Traits\GetArrayCopyTrait;
	
	public function __construct(
		#[CustomAttribute]
		private mixed $value,
	) {}
};

$obj->getArrayCopy() === [
	'value' => 'Random string'
];

$obj = new class (new \DateTimeImmutable('2024-05-24 08:12:42')) {
	use \Starburst\Utils\Traits\GetArrayCopyTrait;
	
	public function __construct(
		#[CustomAttribute]
		private mixed $value,
	) {}
};

$obj->getArrayCopy() === [
	'value' => '24.05 kl. 08:12:42'
];
```
