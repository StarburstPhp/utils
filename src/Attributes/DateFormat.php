<?php declare(strict_types=1);

namespace Starburst\Utils\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class DateFormat
{
	public function __construct(
		public string $format,
	) {}
}
