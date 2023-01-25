<?php declare(strict_types=1);

namespace Starburst\Utils\Traits;

use Starburst\Utils\Exception\MissingRequired;

trait CheckRequiredFieldTrait
{
	/**
	 * @param array<string, mixed> $input
	 * @param string[] $requiredFields
	 * @throws MissingRequired
	 */
	private static function checkRequiredFields(array $input, array $requiredFields): void
	{
		$missing = [];
		foreach ($requiredFields as $field) {
			if (!isset($input[$field])) {
				$missing[] = '"' . $field . '"';
			}
		}
		if ($missing) {
			throw MissingRequired::fields($missing);
		}
	}
}
