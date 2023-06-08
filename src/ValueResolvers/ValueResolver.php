<?php declare(strict_types=1);

namespace Starburst\Utils\ValueResolvers;

interface ValueResolver
{
	public function resolve(mixed $value, \WeakMap $tracker, ?\ReflectionProperty $reflectionProperty = null): mixed;
}
