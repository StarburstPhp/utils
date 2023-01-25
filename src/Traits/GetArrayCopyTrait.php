<?php declare(strict_types=1);

namespace Starburst\Utils\Traits;

use Sunkan\Dictus\DateTimeFormat;
use Sunkan\Dictus\DateTimeFormatter;
use Sunkan\Dictus\Time;
use Sunkan\Dictus\Date;

trait GetArrayCopyTrait
{
	public function getArrayCopy(\WeakMap $tracker = null): array
	{
		$tracker = $tracker ?? new \WeakMap();
		$return = [];
		foreach (get_object_vars($this) as $key => $value) {
			$return[$key] = $this->internalResolveArrayCopyValue($value, $tracker);
		}
		return $return;
	}

	final protected function internalResolveArrayCopyValue(mixed $value, \WeakMap $tracker): mixed
	{
		if ($value === null) {
			return null;
		}

		if (is_array($value)) {
			$returnValue = [];
			foreach ($value as $key => $arrayValue) {
				$returnValue[$key] = $this->internalResolveArrayCopyValue($arrayValue, $tracker);
			}
			return $returnValue;
		}

		if ($value instanceof \DateTimeInterface) {
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

		return $this->resolveArrayCopyValue($value);
	}

	protected function resolveArrayCopyValue(mixed $value): mixed
	{
		return $value;
	}
}
