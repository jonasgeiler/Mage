<?php

namespace Controllers;

use Base;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelQuartile;
use Helpers\ColorParser;
use Helpers\ImageGenerator;
use Helpers\Utils;

class QrCode {

	/**
	 * @param \Base $f3
	 * @param array $params
	 */
	public function render (Base $f3, array $params = []): void {
		ini_set('opcache.enable', '0'); // QR Code Generation doesn't work with Opcache for some reason...

		$path = $params['*'];
		$options = Utils::parsePath($path, [ 'size', 'bgColor', 'fgColor', 'margin', 'ecc', 'encoding' ]);

		$size = (int) ($options['size'] ?? 0);

		$bgColor = ColorParser::parse($options['bgColor'] ?? 'fff');
		$fgColor = ColorParser::parse($options['fgColor'] ?? '000');

		if (isset($options['margin'])) {
			$margin = is_numeric($options['margin']) ? (int) $options['margin'] : null;
		} else {
			$margin = 10;
		}

		$encodingName = $options['encoding'] ?? 'UTF-8';
		$encoding = match (strtoupper($encodingName)) {
			'UTF-8' => new Encoding('UTF-8'),
			'ISO-8859-1' => new Encoding('ISO-8859-1'),
			default => null
		};

		$eccName = $options['ecc'] ?? 'L';
		$ecc = match ($eccName) {
			'L' => new ErrorCorrectionLevelLow(),
			'M' => new ErrorCorrectionLevelMedium(),
			'Q' => new ErrorCorrectionLevelQuartile(),
			'H' => new ErrorCorrectionLevelHigh(),
			default => null,
		};

		$data = Utils::getQueryStr();

		$format = $options['format'] ?? 'png';
		$mime = Utils::getMimeType($format);

		if ($size === 0) {
			$f3->error(400, 'Invalid size!');
		} elseif ($bgColor === null) {
			$f3->error(400, 'Invalid background color!');
		} elseif ($fgColor === null) {
			$f3->error(400, 'Invalid foreground color!');
		} elseif ($margin === null) {
			$f3->error(400, 'Invalid margin!');
		} elseif ($encoding === null) {
			$f3->error(400, 'Invalid encoding!');
		} elseif ($ecc === null) {
			$f3->error(400, 'Invalid ECC!');
		} elseif ($data === null) {
			$f3->error(400, 'Data is required!');
		} elseif ($mime === null) {
			$f3->error(400, 'Unsupported image format!');
		}

		$hash = $f3->hash($size . implode('', $bgColor) . implode('', $fgColor) . $margin . $eccName . $encoding . $data);
		$cachePath = "/i/qr.$hash.$format";

		if (is_file($f3->PUBLIC . $cachePath)) {
			$f3->reroute($cachePath);
		}

		$image = ImageGenerator::generateQRCode($size, $bgColor, $fgColor, $margin, $ecc, $encoding, $data);

		$image->save($f3->PUBLIC . $cachePath);
		$f3->reroute($cachePath);
	}

}
