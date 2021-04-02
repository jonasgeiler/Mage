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
		$options = Utils::parsePath($path, [ 'size', 'blocks' ]);

		$size = (int) ($options['size'] ?? 0);
		$blocks = (int) ($options['blocks'] ?? 4);
		$seed = Utils::getQueryStr() ?? $f3->IP;
		$format = $options['format'] ?? 'png';
		$mime = Utils::getMimeType($format);

		if ($size === 0) {
			$f3->error(400, 'Invalid size!');
		} elseif ($blocks === 0) {
			$f3->error(400, 'Invalid block amount!');
		} elseif ($mime === null) {
			$f3->error(400, 'Unsupported encoding format!');
		}

		$hash = $f3->hash($size . $blocks . $seed);
		$cachePath = "/i/id.$hash.$format";

		if (is_file($f3->PUBLIC . $cachePath)) {
			$f3->reroute($cachePath);
		}

		$image = ImageGenerator::generateIdenticon($seed, $size);

		try {
			$image->save($f3->PUBLIC . $cachePath);
			$f3->reroute($cachePath);
		} catch (NotSupportedException $e) {
			$f3->error(400, "Encoding format ({$format}) is not supported!");
		}
	}

}
