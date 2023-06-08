<?php declare(strict_types=1);

namespace Starburst\Utils\Traits;

use Starburst\Utils\Attributes\HiddenProperty;
use Starburst\Utils\ValueResolvers\ResolverCollection;
use Starburst\Utils\ValueResolvers\ValueResolver;

trait GetArrayCopyTrait
{
	protected static ValueResolver $valueResolver;

	public static function setResolverCollection(ValueResolver $resolver): void
	{
		static::$valueResolver = $resolver;
	}

	public function getArrayCopy(\WeakMap $tracker = null): array
	{
		if (!isset(static::$valueResolver)) {
			static::$valueResolver = ResolverCollection::default();
		}
		$tracker = $tracker ?? new \WeakMap();
		$return = [];

		$objReflection = new \ReflectionClass($this);
		foreach ($objReflection->getProperties() as $property) {
			if ($property->getAttributes(HiddenProperty::class)) {
				continue;
			}

			$return[$property->name] = static::$valueResolver->resolve($property->getValue($this), $tracker, $property);
		}
		return $return;
	}
}
