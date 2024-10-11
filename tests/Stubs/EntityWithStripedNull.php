<?php declare(strict_types=1);

namespace Starburst\Utils\Tests\Stubs;

use Starburst\Utils\Attributes\IgnoreNullValues;
use Starburst\Utils\Traits\GetArrayCopyTrait;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[IgnoreNullValues]
final class EntityWithStripedNull
{
	use GetArrayCopyTrait;

	// @phpstan-ignore-next-line - we are verifying getArrayCopy read private properties
	private ?string $nullable = null;
	public ?EntityWithStripedNull $ref1 = null;
	public ?EntityWithStripedNull $ref2 = null;
	protected UuidInterface $id;

	public function __construct(
		// @phpstan-ignore-next-line - we are verifying getArrayCopy read private properties
		private \DateTimeImmutable $date,
	) {
		$this->id = Uuid::uuid4();
	}
}
