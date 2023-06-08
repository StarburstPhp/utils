<?php declare(strict_types=1);

namespace Starburst\Utils\ValueResolvers;

final class ResolverCollection implements ValueResolver
{
	/** @var array<ValueResolver> */
	private array $resolvers;
	private static ?ValueResolver $defaultInstance = null;

	public static function setDefaultInstance(ValueResolver $resolver): void
	{
		self::$defaultInstance = $resolver;
	}

	public static function default(): ValueResolver
	{
		self::$defaultInstance ??= new self(new DateResolver());
		return self::$defaultInstance;
	}

	public function __construct(
		ValueResolver ...$resolvers,
	) {
		$this->resolvers = $resolvers;
	}

	public function resolve(mixed $value, \WeakMap $tracker, ?\ReflectionProperty $reflectionProperty = null): mixed
	{
		if ($value === null) {
			return null;
		}

		if (is_array($value)) {
			$returnValue = [];
			foreach ($value as $key => $arrayValue) {
				$returnValue[$key] = $this->resolve($arrayValue, $tracker);
			}
			return $returnValue;
		}

		foreach ($this->resolvers as $resolver) {
			$value = $resolver->resolve($value, $tracker, $reflectionProperty);
		}

		if (is_object($value) && method_exists($value, 'getArrayCopy')) {
			if (!isset($tracker[$value])) {
				// tmp value to avoid recursion
				$tracker[$value] = true;
				$tracker[$value] = $value->getArrayCopy($tracker);
			}
			return $tracker[$value];
		}

		if ($value instanceof \JsonSerializable) {
			return $value->jsonSerialize();
		}

		if (is_object($value) && method_exists($value, 'toString')) {
			return $value->toString();
		}

		return $value;
	}
}
