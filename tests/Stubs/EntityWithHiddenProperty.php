<?php declare(strict_types=1);

namespace Starburst\Utils\Tests\Stubs;

use Starburst\Utils\Attributes\HiddenProperty;
use Starburst\Utils\Traits\GetArrayCopyTrait;

final class EntityWithHiddenProperty
{
	use GetArrayCopyTrait;

	public function __construct(
		public string $a,
		#[HiddenProperty]
		public string $b,
		public string $c,
	) {}
}
