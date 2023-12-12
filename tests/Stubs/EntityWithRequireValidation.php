<?php declare(strict_types=1);

namespace Starburst\Utils\Tests\Stubs;

use Starburst\Utils\Traits\CheckRequiredFieldTrait;

final class EntityWithRequireValidation
{
	use CheckRequiredFieldTrait;

	/**
	 * @param array<string, mixed> $data
	 */
	public static function fromInput(array $data): self
	{
		self::checkRequiredFields($data, ['id', 'data']);

		return new self($data['id'], $data['data']);
	}

	public function __construct(
		public mixed $id,
		public mixed $data,
	) {}
}
