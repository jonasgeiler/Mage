<?php

namespace Libraries\QrCode;

use Endroid\QrCode\Writer\Result\AbstractResult;
use Intervention\Image\Image;

class CustomResult extends AbstractResult {

	/**
	 * @var \Intervention\Image\Image
	 */
	private Image $image;

	/**
	 * CustomResult constructor.
	 *
	 * @param \Intervention\Image\Image $image
	 */
	public function __construct (Image $image) {
		$this->image = $image;
	}

	/**
	 * @return \Intervention\Image\Image
	 */
	public function getImage (): Image {
		return $this->image;
	}

	/**
	 * @return string
	 */
	public function getString (): string {
		return (string) $this->image->encode('png');
	}

	/**
	 * @return string
	 */
	public function getMimeType (): string {
		return 'image/png';
	}
}
