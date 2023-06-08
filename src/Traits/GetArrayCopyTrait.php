<?php declare(strict_types=1);

namespace Starburst\Utils\Traits;

use Starburst\Utils\Attributes\HiddenProperty;
use Starburst\Utils\ValueResolvers\ResolverCollection;

trait GetArrayCopyTrait
{
	public function getArrayCopy(\WeakMap $tracker = null): array
	{
		$valueResolver = ResolverCollection::default();

		$tracker = $tracker ?? new \WeakMap();
		$return = [];

		$objReflection = new \ReflectionClass($this);
		foreach ($objReflection->getProperties() as $property) {
			if ($property->getAttributes(HiddenProperty::class)) {
				continue;
			}

			$return[$property->name] = $valueResolver->resolve($property->getValue($this), $tracker, $property);
		}
		return $return;
	}
}
