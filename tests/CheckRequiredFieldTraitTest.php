<?php declare(strict_types=1);

namespace Starburst\Utils\Tests;

use PHPUnit\Framework\TestCase;
use Starburst\Utils\Exception\MissingRequired;
use Starburst\Utils\Tests\Stubs\EntityWithRequireValidation;

final class CheckRequiredFieldTraitTest extends TestCase
{
	public function testMissingField(): void
	{
		$this->expectException(MissingRequired::class);

		EntityWithRequireValidation::fromInput(['data' => 'test']);
	}

	public function testPassing(): void
	{
		$entity = EntityWithRequireValidation::fromInput(['id' => '1', 'data' => 'test']);

		$this->assertSame('1', $entity->id);
		$this->assertSame('test', $entity->data);
	}

	public function testExceptionContainsCorrectFields(): void
	{
		$missingField = ['"data"'];

		$this->expectException(MissingRequired::class);
		$this->expectExceptionMessage(MissingRequired::fields($missingField)->getMessage());

		EntityWithRequireValidation::fromInput(['id' => 1]);
	}
}
