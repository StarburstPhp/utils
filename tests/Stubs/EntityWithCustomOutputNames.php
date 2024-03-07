<?php declare(strict_types=1);

namespace Starburst\Utils\Tests\Stubs;

use Starburst\Utils\Attributes\CustomName;
use Starburst\Utils\Traits\GetArrayCopyTrait;

final class EntityWithCustomOutputNames
{
	use GetArrayCopyTrait;

	public function __construct(
		public string $a,
		#[CustomName('test')]
		public string $b,
		public string $c,
	) {}
}
