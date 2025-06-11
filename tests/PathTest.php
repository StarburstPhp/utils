<?php declare(strict_types=1);

namespace Starburst\Utils\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Starburst\Utils\Path;

final class PathTest extends TestCase
{
	/**
	 * @return list<array{string, string}>
	 */
	public static function provideCanonicalizationTests(): array
	{
		return [
			// relative paths (forward slash)
			['css/./style.css', 'css/style.css'],
			['css/../style.css', 'style.css'],
			['css/./../style.css', 'style.css'],
			['css/.././style.css', 'style.css'],
			['css/../../style.css', '../style.css'],
			['./css/style.css', 'css/style.css'],
			['../css/style.css', '../css/style.css'],
			['./../css/style.css', '../css/style.css'],
			['.././css/style.css', '../css/style.css'],
			['../../css/style.css', '../../css/style.css'],
			['', ''],
			['.', ''],
			['..', '..'],
			['./..', '..'],
			['../.', '..'],
			['../..', '../..'],

			// relative paths (backslash)
			['css\\.\\style.css', 'css/style.css'],
			['css\\..\\style.css', 'style.css'],
			['css\\.\\..\\style.css', 'style.css'],
			['css\\..\\.\\style.css', 'style.css'],
			['css\\..\\..\\style.css', '../style.css'],
			['.\\css\\style.css', 'css/style.css'],
			['..\\css\\style.css', '../css/style.css'],
			['.\\..\\css\\style.css', '../css/style.css'],
			['..\\.\\css\\style.css', '../css/style.css'],
			['..\\..\\css\\style.css', '../../css/style.css'],

			// absolute paths (forward slash, UNIX)
			['/css/style.css', '/css/style.css'],
			['/css/./style.css', '/css/style.css'],
			['/css/../style.css', '/style.css'],
			['/css/./../style.css', '/style.css'],
			['/css/.././style.css', '/style.css'],
			['/./css/style.css', '/css/style.css'],
			['/../css/style.css', '/css/style.css'],
			['/./../css/style.css', '/css/style.css'],
			['/.././css/style.css', '/css/style.css'],
			['/../../css/style.css', '/css/style.css'],

			// absolute paths (backslash, UNIX)
			['\\css\\style.css', '/css/style.css'],
			['\\css\\.\\style.css', '/css/style.css'],
			['\\css\\..\\style.css', '/style.css'],
			['\\css\\.\\..\\style.css', '/style.css'],
			['\\css\\..\\.\\style.css', '/style.css'],
			['\\.\\css\\style.css', '/css/style.css'],
			['\\..\\css\\style.css', '/css/style.css'],
			['\\.\\..\\css\\style.css', '/css/style.css'],
			['\\..\\.\\css\\style.css', '/css/style.css'],
			['\\..\\..\\css\\style.css', '/css/style.css'],

			// trailing slash
			['public/styles', 'public/styles'],
			['public/styles/', 'public/styles'],


			// with schemas
			['phar:///css/style.css', 'phar:///css/style.css'],
			['phar://css/style.css', 'phar://css/style.css'],
			['phar:///css/./style.css', 'phar:///css/style.css'],
			['phar:///css/../style.css', 'phar:///style.css'],
			['phar:///css/./../style.css', 'phar:///style.css'],
			['phar:///css/.././style.css', 'phar:///style.css'],
			['phar:///./css/style.css', 'phar:///css/style.css'],
			['phar:///../css/style.css', 'phar:///css/style.css'],
			['phar:///./../css/style.css', 'phar:///css/style.css'],
			['phar:///.././css/style.css', 'phar:///css/style.css'],
			['phar:///../../css/style.css', 'phar:///css/style.css'],
		];
	}

	#[DataProvider('provideCanonicalizationTests')]
	public function testCanonicalize(string $path, string $canonicalized): void
	{
		$this->assertSame($canonicalized, Path::canonicalize($path));
	}

	/**
	 * @return list<array{0: list<string>, 1: string}>
	 */
	public static function provideJoinTests(): array
	{
		return [
			[['', ''], ''],
			[['/path/to/test', ''], '/path/to/test'],
			[['/path/to//test', ''], '/path/to/test'],
			[['', '/path/to/test'], '/path/to/test'],
			[['', '/path/to//test'], '/path/to/test'],

			[['/path/to/test', 'subdir'], '/path/to/test/subdir'],
			[['/path/to/test/', 'subdir'], '/path/to/test/subdir'],
			[['/path/to/test', '/subdir'], '/path/to/test/subdir'],
			[['/path/to/test/', '/subdir'], '/path/to/test/subdir'],
			[['/path/to/test', './subdir'], '/path/to/test/subdir'],
			[['/path/to/test/', './subdir'], '/path/to/test/subdir'],
			[['/path/to/test/', '../parentdir'], '/path/to/parentdir'],
			[['/path/to/test', '../parentdir'], '/path/to/parentdir'],
			[['path/to/test/', '/subdir'], 'path/to/test/subdir'],
			[['path/to/test', '/subdir'], 'path/to/test/subdir'],
			[['../path/to/test', '/subdir'], '../path/to/test/subdir'],
			[['path', '../../subdir'], '../subdir'],
			[['/path', '../../subdir'], '/subdir'],
			[['../path', '../../subdir'], '../../subdir'],

			[['/path/to/test', 'subdir'], '/path/to/test/subdir'],
			[['/path/to/test', '/subdir'], '/path/to/test/subdir'],
			[['/path/to/test/', 'subdir'], '/path/to/test/subdir'],
			[['/path/to/test/', '/subdir'], '/path/to/test/subdir'],

			[['/path'], '/path'],
			[['/path', 'to', '/test'], '/path/to/test'],
			[['/path', '', '/test'], '/path/test'],
			[['path', 'to', 'test'], 'path/to/test'],
			[[], ''],

			[['base/path', 'to/test'], 'base/path/to/test'],

			[['/', 'subdir'], '/subdir'],
			[['/', '/subdir'], '/subdir'],

			[['phar://my_phar.phar', 'path', 'to', 'file.txt'], 'phar://my_phar.phar/path/to/file.txt'],
		];
	}

	/**
	 * @param list<string> $parts
	 */
	#[DataProvider('provideJoinTests')]
	public function testJoin(array $parts, string $result): void
	{
		$this->assertSame($result, Path::join(...$parts));
	}

	public function testJoinVarArgs(): void
	{
		$this->assertSame('/path', Path::join('/path'));
		$this->assertSame('/path/to', Path::join('/path', 'to'));
		$this->assertSame('/path/to/test', Path::join('/path', 'to', '/test'));
		$this->assertSame('/path/to/test/subdir', Path::join('/path', 'to', '/test', 'subdir/'));
	}

	#[TestWith(['\Foo\Bar\test', '/Foo/Bar/test'])]
	#[TestWith(['\Foo\..\Bar\test', '/Foo/../Bar/test'])]
	#[TestWith(['/public/styles', '/public/styles'])]
	#[TestWith(['/public/styles/', '/public/styles'])]
	public function testNormalize(string $path, string $expected): void
	{
		$this->assertSame($expected, Path::normalize($path));
	}
}
