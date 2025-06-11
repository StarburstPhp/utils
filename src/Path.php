<?php declare(strict_types=1);

namespace Starburst\Utils;

final class Path
{
	/**
	 * Canonicalizes the given path.
	 *
	 * During normalization, all slashes are replaced by forward slashes ("/").
	 * Furthermore, all "." and ".." segments are removed as far as possible.
	 * ".." segments at the beginning of relative paths are not removed.
	 *
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
		$parts = preg_split('# *[/\\\\]+ *#', trim($path, ' '));
		if ($parts === false) {
			throw new \InvalidArgumentException(sprintf('The path "%s" is not valid.', $path));
		}
		$canonicalParts = [];

		// Collapse "." and "..", if possible
		foreach ($parts as $part) {
			if ($part === '.' || $part === '') {
				continue;
			}

			// Collapse ".." with the previous part, if one exists
			// Don't collapse ".." if the previous part is also ".."
			if (
				$part === '..' && count($canonicalParts) > 0
				&& '..' !== $canonicalParts[count($canonicalParts) - 1]
			) {
				array_pop($canonicalParts);

				continue;
			}

			// Only add ".." prefixes for relative paths
			if ('..' !== $part || '' === $root) {
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
	 * or dot path segments. Consequently, it is much more efficient and should
	 * be used whenever the given path is known to be a valid, absolute system
	 * path.
	 */
	public static function normalize(string $path): string
	{
		return rtrim(str_replace('\\', '/', $path), '/');
	}

	public static function join(string ...$parts): string
	{
		$finalPath = null;
		$wasScheme = false;

		foreach ($parts as $path) {
			if ($path === '') {
				continue;
			}

			if ($finalPath === null) {
				// For the first part we keep slashes, like '/top', 'C:\' or 'phar://'
				$finalPath = $path;
				$wasScheme = (str_contains($path, '://'));
				continue;
			}

			// Only add slash if the previous part didn't end with '/' or '\'
			if (!in_array(substr($finalPath, -1), ['/', '\\'])) {
				$finalPath .= '/';
			}

			// If the first part included a scheme like 'phar://' allow current part to start with '/'
			$finalPath .= $wasScheme ? $path : ltrim($path, '/');
			$wasScheme = false;
		}

		if ($finalPath === null) {
			return '';
		}

		return self::canonicalize($finalPath);
	}
}
