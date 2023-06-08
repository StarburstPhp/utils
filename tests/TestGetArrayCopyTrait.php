<?php declare(strict_types=1);

namespace Starburst\Utils\Tests;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Starburst\Utils\Tests\Stubs\CustomDateFormatEntity;
use Starburst\Utils\Tests\Stubs\CustomValueResolver;
use Starburst\Utils\Tests\Stubs\EntityWithHiddenProperty;
use Starburst\Utils\Tests\Stubs\EntityWithSelf;
use Starburst\Utils\Tests\Stubs\TimeEntity;
use Starburst\Utils\ValueResolvers\ResolverCollection;
use Sunkan\Dictus\Date;
use Sunkan\Dictus\DateParser;

class TestGetArrayCopyTrait extends TestCase
{
	protected function setUp(): void
	{
		ResolverCollection::setDefaultInstance(null);
	}

	public function testInfiniteRecursion(): void
	{
		$date1 = new \DateTimeImmutable('2021-12-13 00:00:00');
		$date2 = new \DateTimeImmutable('2021-12-14 00:00:00');
		$entity1 = new EntityWithSelf($date1);
		$entity2 = new EntityWithSelf($date2);
		$entity1->ref1 = $entity1;
		$entity1->ref2 = $entity2;

		$entity2->ref1 = $entity1;

		$array = $entity1->getArrayCopy();

		$this->assertIsString($array['id']);
		$this->assertTrue(Uuid::isValid($array['id']));
		$this->assertSame('2021-12-13T00:00:00Z', $array['date']);
	}

	public function testTimeFormatting(): void
	{
		$entity = new TimeEntity(
			DateParser::fromTime('12:54'),
			DateParser::fromString('2021-12-13 12:54:41'),
			DateParser::fromDay('2022-05-13'),
		);

		$array = $entity->getArrayCopy();
		$this->assertSame('12:54:00', $array['time']);
		$this->assertSame('2021-12-13T12:54:41Z', $array['date']);
		$this->assertSame('2022-05-13', $array['day']);
	}

	public function testCustomDateFormateValue(): void
	{
		$start = DateParser::fromTime('12:54');
		$end = DateParser::fromTime('15:36');
		$date = new Date('2022-05-08');

		$entity = new CustomDateFormatEntity(
			$start,
			$end,
			$date,
		);

		$array = $entity->getArrayCopy();
		$this->assertSame('12:54:00', $array['start']);
		$this->assertSame('15:36:00', $array['end']);
		$this->assertSame('05.08.2022', $array['date']);
	}

	public function testHiddenProperty(): void
	{
		$entity = new EntityWithHiddenProperty('a', 'b', 'c');

		$array = $entity->getArrayCopy();
		$this->assertArrayNotHasKey('b', $array);
		$this->assertSame('a', $array['a']);
		$this->assertSame('c', $array['c']);
	}

	public function testCustomValueResolver(): void
	{
		ResolverCollection::setDefaultInstance(new ResolverCollection(new CustomValueResolver()));
		$entity = new EntityWithHiddenProperty('test', 'b', 'c');

		$array = $entity->getArrayCopy();
		$this->assertNotSame($entity->a, $array['a']);
		$this->assertSame(42, $array['a']);
	}
}
