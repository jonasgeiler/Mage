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
				$options['mime'] = static::getMimeType($subParts[1]);
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
	 * Checks if the memory needed to generate the image doesn't exceed limits
	 *
	 * @param int $width
	 * @param int $height
	 * @param int $rgb
	 *
	 * @return bool
	 */
	public static function hasEnoughMemory ($width, $height, $rgb = 3): bool {
		$f3 = Base::instance();

		return ($width * $height * $rgb * 1.7 < $f3->MEMORY_LIMIT - memory_get_usage());
	}
}
