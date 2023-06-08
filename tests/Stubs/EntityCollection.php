<?php declare(strict_types=1);

namespace Starburst\Utils\Tests\Stubs;

use Starburst\Utils\Traits\GetArrayCopyTrait;

final class EntityCollection
{
	use GetArrayCopyTrait;

	/** @var list<StubEntity> */
	public array $entities = [];
}
