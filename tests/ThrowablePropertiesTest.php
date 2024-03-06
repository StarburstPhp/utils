<?php declare(strict_types=1);

namespace Starburst\Utils\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Starburst\Utils\ThrowableProperties;
use Starburst\Utils\Tests\Stubs\FakeException;
use Throwable;

final class ThrowablePropertiesTest extends TestCase
{
	public function testBasic(): void
	{
		try {
			$prev = new Exception('prev message');
			$line = __LINE__ + 1;
			throw new FakeException('fake message', 88, $prev);
		}
		catch (Throwable $e) {
			$t = ThrowableProperties::fromThrowable($e);
			$this->assertInstanceOf(ThrowableProperties::class, $t);
			$this->assertSame(FakeException::class, $t->class);
			$this->assertSame('fake message', $t->message);
			$this->assertSame(88, $t->code);
			$this->assertSame(__FILE__, $t->file);
			$this->assertSame($line, $t->line);
			$this->assertSame(['foo' => 'bar', 'baz' => 'dib'], $t->other);
			$this->assertNotEmpty($t->trace);
			foreach ($t->trace as $info) {
				// @phpstan-ignore-next-line - we are verifying it can't happen
				$this->assertFalse(array_key_exists('args', $info));
			}
			$this->assertInstanceOf(ThrowableProperties::class, $t->previous);
			$this->assertSame((string) $e, (string) $t);
		}
	}

	public function testJsonEncode(): void
	{
		try {
			$prev = new Exception('prev message');
			$line = __LINE__ + 1;
			throw new FakeException('fake message', 88, $prev);
		}
		catch (Throwable $e) {
			if (!extension_loaded('xdebug')) {
				$this->assertSame('{}', json_encode($e));
			}
			$t = ThrowableProperties::fromThrowable($e);
			/** @var \stdClass $j */
			$j = json_decode((string)json_encode($t));
			$this->assertSame(FakeException::class, $j->class);
			$this->assertSame('fake message', $j->message);
			$this->assertSame(88, $j->code);
			$this->assertSame(__FILE__, $j->file);
			$this->assertSame($line, $j->line);
			$this->assertEquals((object) ['foo' => 'bar', 'baz' => 'dib'], $j->other);
			$this->assertNotEmpty($j->trace);
			foreach ($j->trace as $info) {
				$this->assertFalse(property_exists($info, 'args'));
			}
			$this->assertNotEmpty($j->previous);
		}
	}
}
