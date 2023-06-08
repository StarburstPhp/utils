<?php declare(strict_types=1);

namespace Starburst\Utils\Tests\Stubs;

use Starburst\Utils\Traits\GetArrayCopyTrait;

final class TimeEntity
{
	use GetArrayCopyTrait;

	public function __construct(
		protected \DateTimeImmutable $time,
		protected \DateTimeImmutable $date,
		protected \DateTimeImmutable $day,
	) {}
}
