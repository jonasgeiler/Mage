<?php

namespace Helpers;

use Base;
use Intervention\Image\AbstractFont;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Jdenticon\Identicon;
use Libraries\JdenticonRenderer;

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
	 * @param string $seed Can be a username, IP, etc.
	 * @param int    $size
	 *
	 * @return \Intervention\Image\Image
	 */
	public static function generateIdenticon (string $seed, int $size): Image {
		$renderer = new JdenticonRenderer($size, static::manager());

		$identicon = new Identicon();
		$identicon->setValue($seed)
		          ->setSize($size);
		$identicon->draw($renderer);

		return $renderer->getImage();
	}

}
