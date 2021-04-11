<?php

namespace Controllers;

use Base;
use Helpers\ImageGenerator;
use Helpers\Utils;
use Image;
use Intervention\Image\Exception\NotSupportedException;

class Identicon {

	/**
	 * @param \Base $f3
	 * @param array $params
	 */
	public function render (Base $f3, array $params = []): void {
		$path = $params['*'];
		$options = Utils::parsePath($path, [ 'size' ]);

		$size = (int) ($options['size'] ?? 0);

		$seed = Utils::getQueryStr() ?? $f3->IP;

		$format = $options['format'] ?? 'png';
		$mime = Utils::getMimeType($format);

		if ($size === 0) {
			$f3->error(400, 'Invalid size!');
		} elseif ($mime === null) {
			$f3->error(400, 'Unsupported image format!');
		} elseif (Utils::isTooLarge($size)) {
			$f3->error(400, 'Requested image size exceeds limits!');
		}

		$hash = $f3->hash($size . $seed);
		$cachePath = "/i/id.$hash.$format";

		if (is_file($f3->PUBLIC . $cachePath)) {
			$f3->reroute($cachePath);
		}

		$image = ImageGenerator::generateIdenticon($size, $seed);

		$image->save($f3->PUBLIC . $cachePath);
		$f3->reroute($cachePath);
	}

}
