<?php declare(strict_types=1);

namespace Starburst\Utils\Traits;

use Starburst\Utils\Attributes\CustomName;
use Starburst\Utils\Attributes\HiddenProperty;
use Starburst\Utils\Attributes\IgnoreNullValues;
use Starburst\Utils\ValueResolvers\ResolverCollection;

trait GetArrayCopyTrait
{
	/**
	 * @param \WeakMap<object, mixed>|null $tracker
	 * @return array<string, mixed>
	 */
	public function getArrayCopy(?\WeakMap $tracker = null): array
	{
		$valueResolver = ResolverCollection::default();

		$tracker = $tracker ?? new \WeakMap();
		$return = [];

		$objReflection = new \ReflectionClass($this);
		$stripNullValues = $objReflection->getAttributes(IgnoreNullValues::class) ?: false;
		foreach ($objReflection->getProperties() as $property) {
			if ($property->getAttributes(HiddenProperty::class)) {
				continue;
			}
			$propertyName = $property->name;
			$customNameAttributes = $property->getAttributes(CustomName::class);
			if ($customNameAttributes) {
				$propertyName = $customNameAttributes[0]->newInstance()->name;
			}

			// Needed for php 8.0. See https://www.php.net/manual/en/reflectionproperty.getvalue.php
			$property->setAccessible(true);

			$resolvedValue = $valueResolver->resolve($property->getValue($this), $tracker, $property);
			if ($stripNullValues && $resolvedValue === null) {
				continue;
			}
			$return[$propertyName] = $resolvedValue;
		}
		return $return;
	}
}
