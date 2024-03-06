<?php declare(strict_types=1);

namespace Starburst\Utils\Tests\Stubs;

use Starburst\Utils\ValueResolvers\ValueResolver;

final class CustomValueResolver implements ValueResolver
{
	public function resolve(
		mixed $value,
		\WeakMap $tracker,
		?\ReflectionProperty $reflectionProperty = null,
	): mixed {
		if ($value === 'test') {
			return 42;
		}
		return $value;
	}
}
