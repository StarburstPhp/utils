## Json

The `Starburst\Utils\Json` class provides static methods for encoding and 
decoding Json in PHP.
Below, you'll find detailed documentation on how to use it:

### `encode(mixed $value): string`

This method accepts a variable of any type and converts it to a JSON string.

**Parameters:**

* `$value (mixed)`: The value you want to encode to JSON.

**Returns:**

* JSON Encoded string

**Usage:**
```php
$jsonString = \Starburst\Utils\Json::encode(["name" => "John Doe", "age" => 30]);
```

### `decode(string $value): mixed`

This method accepts a JSON string and decodes it to the corresponding PHP value or array.

**Parameters:**

* `$value (string)`: The JSON string you want to decode.

**Returns:**

* The PHP value that was encoded in the JSON string. If json is an object it's returned as an assoc array

**Usage:**
```php
$phpValue = \Starburst\Utils\Json::decode($jsonString);
```

### `decodeArray(string $value): array`

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

### `decodeList(string $value): array`

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

## Validators

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

### `isKennitala(mixed $value): bool`

This function checks if the provided value is a valid kennitala.

**Parameters:**

* `$value (mixed)`: The value you want to check.

**Returns:**

* True or false

**Usage:**

```php
$isValid = \Starburst\Utils\Validators::isKennitala($value);
```

### `isEmail(string $value): bool`

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
