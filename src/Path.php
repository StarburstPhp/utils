<?php declare(strict_types=1);

namespace Starburst\Utils;

final class Path
{
	/**
	 * Canonicalizes the given path.
	 *
	 * During canonicalizing, all slashes are replaced by forward slashes ("/").
	 * Furthermore, all "." and ".." segments are removed as far as possible.
	 * ".." segments at the beginning of relative paths are not removed.
	 *
	 * ```php
	 *     echo Path::canonicalize("\starburst\public\..\css\style.css");
	 *     // => /starburst/css/style.css
	 *
	 *     echo Path::canonicalize("../css/./style.css");
	 *     // => ../css/style.css
	 * ```
	 */
	public static function canonicalize(string $path): string
	{
		if ($path === '') {
			return '';
		}

		$path = str_replace('\\', '/', $path);
		$schemaRoot = strstr($path, '://', true) ?: '';
		if ($schemaRoot) {
			$schemaRoot .= '://';
			$path = substr($path, strlen($schemaRoot));
		}
		$root = $path[0] === '/' ? '/' : '';
		$parts = explode('/', trim($path, ' '));
		$canonicalParts = [];

		foreach ($parts as $part) {
			if ($part === '.' || $part === '') {
				continue;
			}

			// Only collapse ".." if the previous part is not ".."
			if (
				$part === '..'
				&& count($canonicalParts) > 0
				&& $canonicalParts[count($canonicalParts) - 1] !== '..'
			) {
				array_pop($canonicalParts);

				continue;
			}

			if ($part !== '..' || $root === '') {
				$canonicalParts[] = $part;
			}
		}

		return $schemaRoot . $root . implode('/', $canonicalParts);
	}

	/**
	 * Normalizes the given path.
	 *
	 * During normalization, all slashes are replaced by forward slashes ("/").
	 * Contrary to {@link canonicalize()}, this method does not remove invalid
	 * or dot path segments.
	 *
	 * This method should mainly be used to normalize paths that are known to be valid
	 *
	 *  ```php
	 *      echo Path::normalize("\starburst\public\..\css\style.css");
	 *      // => /starburst/public/../css/style.css
	 *  ```
	 *
	 */
	public static function normalize(string $path): string
	{
		return rtrim(str_replace('\\', '/', $path), '/');
	}

	public static function join(string ...$parts): string
	{
		$finalPath = '';
		$root = null;

		foreach ($parts as $path) {
			if ($path === '') {
				continue;
			}

			if ($root === null) {
				$root = str_contains($path, '://') ? $path : ($path[0] === '/' ? '/' : '');
				$finalPath .= substr($path, strlen($root));
				continue;
			}

			// Only add slash if the previous part didn't end with '/' or '\'
			if (!in_array(substr($finalPath, -1), ['/', '\\'])) {
				$finalPath .= '/';
			}

			$finalPath .= ltrim($path, '/');
		}

		if (!$finalPath) {
			return '';
		}

		return self::canonicalize($root . $finalPath);
	}
}
