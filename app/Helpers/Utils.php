<?php

namespace Helpers;

use Base;

class Utils {

	/**
	 * @param string $path
	 * @param array  $sections
	 *
	 * @return array
	 */
	public static function parsePath (string $path, array $sections = []): array {
		$parts = explode('/', $path);
		$options = [
			'format' => null
		];

		foreach ($parts as $i => $part) {
			$section = $sections[$i] ?? $i;

			if (trim($part) === '') {
				continue;
			}

			if (str_contains($part, '.')) {
				$subParts = explode('.', $part);

				if (trim($subParts[0]) === '' || trim($subParts[1]) === '') {
					continue; // Part is invalid
				}

				$part = $subParts[0];
				$options['format'] = $subParts[1];
			}

			if (is_array($section)) {
				$subParts = explode('x', $part);

				foreach ($subParts as $j => $subPart) {
					$subSection = $section[$j] ?? $j;

					if (trim($subPart) === '') {
						continue;
					}

					$options[$subSection] = $subPart;
				}
			} else {
				$options[$section] = $part;
			}
		}

		return $options;
	}

	/**
	 * Get mime type for a format
	 *
	 * @param string $format
	 *
	 * @return string|null
	 */
	public static function getMimeType(string $format): ?string {
		return match ($format) {
			'gif' => 'image/gif',
			'png' => 'image/png',
			'webp' => 'image/webp',
			'jpg', 'jpeg', 'jfif', 'pjpeg', 'pjp' => 'image/jpeg',
			default => null,
		};
	}

	/**
	 * @return string|null
	 */
	public static function getQueryStr (): ?string {
		$f3 = Base::instance();
		$queryStr = trim($f3->QUERY);

		if ($queryStr) {
			return urldecode($queryStr);
		}

		return null;
	}

	/**
	 * Checks if given width / height exceeds the maximum megapixel amount
	 *
	 * @param int $width
	 * @param int|null $height
	 *
	 * @return bool
	 */
	public static function isTooLarge (int $width, int $height = null): bool {
		$f3 = Base::instance();
		$height ??= $width;

		return ($width * $height) / 1000000 > $f3->MAX_MEGAPIXEL;
	}
}
