<?php declare(strict_types=1);

namespace Starburst\Utils\Tests\Stubs;

final class FakeException extends \Exception
{
	protected string $foo = 'bar';
	protected string $baz = 'dib';
}
