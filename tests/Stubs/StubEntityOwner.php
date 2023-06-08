<?php declare(strict_types=1);

namespace Starburst\Utils\Tests\Stubs;

use Starburst\Utils\Traits\GetArrayCopyTrait;

final class StubEntityOwner
{
	use GetArrayCopyTrait;

	public function __construct(
		private string $name,
	) {}
}
