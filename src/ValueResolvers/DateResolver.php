<?php declare(strict_types=1);

namespace Starburst\Utils\ValueResolvers;

use Starburst\Utils\Attributes\DateFormat;
use Sunkan\Dictus\Date;
use Sunkan\Dictus\DateTimeFormat;
use Sunkan\Dictus\DateTimeFormatter;
use Sunkan\Dictus\Time;

final class DateResolver implements ValueResolver
{
	public function resolve(mixed $value, \WeakMap $tracker, ?\ReflectionProperty $reflectionProperty = null): mixed
	{
		if (!$value instanceof \DateTimeInterface) {
			return $value;
		}
		if ($attrs = $reflectionProperty?->getAttributes(DateFormat::class)) {
			/** @var DateFormat $formatInstance */
			$formatInstance = $attrs[0]->newInstance();
			return (new DateTimeFormatter($formatInstance->format))->format($value);
		}
		if ($value instanceof Time) {
			static $timeFormatter = null;
			$timeFormatter = $timeFormatter ?? new DateTimeFormatter(DateTimeFormat::TIME);
			return $timeFormatter->format($value);
		}
		if ($value instanceof Date) {
			static $dayFormatter = null;
			$dayFormatter = $dayFormatter ?? new DateTimeFormatter(DateTimeFormat::DATE);
			return $dayFormatter->format($value);
		}
		static $dateFormatter = null;
		$dateFormatter = $dateFormatter ?? new DateTimeFormatter(DateTimeFormat::JSON);
		return $dateFormatter->format($value);
	}
}
