<?php declare(strict_types=1);

namespace Starburst\Utils\Tests\Stubs;

use Starburst\Utils\Attributes\DateFormat;
use Starburst\Utils\Traits\GetArrayCopyTrait;

final class CustomDateFormatEntity
{
	use GetArrayCopyTrait;

	public function __construct(
		protected \DateTimeImmutable $start,
		protected \DateTimeImmutable $end,
		#[DateFormat('m.d.Y')]
		protected \DateTimeImmutable $date,
	) {}
}
