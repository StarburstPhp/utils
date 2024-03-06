<?php declare(strict_types=1);

namespace Starburst\Utils\ValueResolvers;

interface ValueResolver
{
	/**
	 * @param \WeakMap<object, mixed> $tracker
	 */
	public function resolve(mixed $value, \WeakMap $tracker, ?\ReflectionProperty $reflectionProperty = null): mixed;
}
