<?php

namespace Helpers;

use Base;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelInterface;
use Endroid\QrCode\QrCode;
use Intervention\Image\AbstractFont;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Jdenticon\Identicon;
use Libraries\Jdenticon\CustomRenderer as JdenticonRenderer;
use Libraries\QrCode\CustomWriter as QrCodeWriter;

class ImageGenerator {

	/**
	 * @var \Intervention\Image\ImageManager
	 */
	private static $manager;

	/**
	 * Returns the image manager
	 *
	 * @return \Intervention\Image\ImageManager
	 */
	private static function manager (): ImageManager {
		if (!static::$manager) {
			static::$manager = new ImageManager();
		}

		return static::$manager;
	}

	/**
	 * Increases font size until font doesn't fit anymore
	 *
	 * @param \Intervention\Image\AbstractFont $font
	 * @param int                              $width
	 * @param int                              $height
	 */
	private static function fitFont (AbstractFont $font, int $width, int $height): void {
		$currentSize = 0;
		$xMargin = $width * 0.2;
		$yMargin = $height * 0.2;

		do {
			$currentSize++;

			$font->size($currentSize);
			$box = $font->getBoxSize();
		} while ($box['width'] < $width - $xMargin && $box['height'] < $height - $yMargin);

		$font->size($currentSize - 1);
	}

	/**
	 * @param int    $width
	 * @param int    $height
	 * @param array  $bgColor
	 * @param array  $textColor
	 * @param string $text
	 *
	 * @return \Intervention\Image\Image
	 */
	public static function generatePlaceholder (int $width, int $height, array $bgColor, array $textColor, string $text): Image {
		$image = static::manager()->canvas($width, $height, $bgColor);

		$image->text(
			$text,
			$width / 2,
			$height / 2,

			/**
			 * @param \Intervention\Image\AbstractFont $font
			 */
			static function (AbstractFont $font) use ($textColor, $width, $height) {
				$f3 = Base::instance();

				$font->file($f3->FONTS . 'OpenSans-Bold.ttf');
				$font->color($textColor);
				$font->align('center');
				$font->valign('center');

				static::fitFont($font, $width, $height);
			}
		);

		return $image;
	}

	/**
	 * Generate an Identicon using the Jdenticon library and a custom renderer
	 *
	 * @param int    $size
	 * @param string $seed Can be a username, IP, etc.
	 *
	 * @return \Intervention\Image\Image
	 */
	public static function generateIdenticon (int $size, string $seed): Image {
		$renderer = new JdenticonRenderer($size, static::manager());

		$identicon = new Identicon();
		$identicon->setValue($seed)
		          ->setSize($size);
		$identicon->draw($renderer);

		return $renderer->getImage();
	}

	/**
	 * @param int                                                                $size
	 * @param array                                                              $bgColor
	 * @param array                                                              $fgColor
	 * @param int                                                                $margin
	 * @param \Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelInterface $ecc
	 * @param \Endroid\QrCode\Encoding\Encoding                                  $encoding
	 * @param string                                                             $data
	 *
	 * @return \Intervention\Image\Image
	 */
	public static function generateQRCode (int $size, array $bgColor, array $fgColor, int $margin, ErrorCorrectionLevelInterface $ecc, Encoding $encoding, string $data): Image {
		$qrCode = QrCode::create($data)
		                ->setSize($size - $margin * 2)
		                ->setBackgroundColor(new Color(...$bgColor))
		                ->setForegroundColor(new Color(...$fgColor))
		                ->setMargin($margin)
		                ->setErrorCorrectionLevel($ecc)
		                ->setEncoding($encoding);

		$writer = new QrCodeWriter(static::manager());

		return $writer->write($qrCode)
		              ->getImage();
	}

}
