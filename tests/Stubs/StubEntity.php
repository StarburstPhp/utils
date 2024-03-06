<?php declare(strict_types=1);

namespace Starburst\Utils\Tests\Stubs;

use Starburst\Utils\Traits\GetArrayCopyTrait;
use Ramsey\Uuid\UuidInterface;

final class StubEntity
{
	use GetArrayCopyTrait;

	public function __construct(
		public UuidInterface $id,
		public StubEntityOwner $owner,
	) {}
}
