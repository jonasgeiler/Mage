<?php

namespace Libraries\QrCode;

use Endroid\QrCode\Bacon\MatrixFactory;
use Endroid\QrCode\Color\ColorInterface;
use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\WriterInterface;
use Intervention\Image\AbstractShape;
use Intervention\Image\ImageManager;

class CustomWriter implements WriterInterface {

	/**
	 * @var \Intervention\Image\ImageManager
	 */
	private ImageManager $imageManager;

	public function __construct (ImageManager $imageManager) {
		$this->imageManager = $imageManager;
	}

	/**
	 * @param \Endroid\QrCode\QrCodeInterface           $qrCode
	 * @param \Endroid\QrCode\Logo\LogoInterface|null   $logo
	 * @param \Endroid\QrCode\Label\LabelInterface|null $label
	 * @param array<mixed>                              $options
	 *
	 * @return \Libraries\QrCode\CustomResult;
	 */
	public function write (QrCodeInterface $qrCode, LogoInterface $logo = null, LabelInterface $label = null, array $options = []): CustomResult {
		$matrixFactory = new MatrixFactory();
		$matrix = $matrixFactory->create($qrCode);

		$backgroundColor = $this->colorToArray($qrCode->getBackgroundColor());
		$foregroundColor = $this->colorToArray($qrCode->getForegroundColor());

		$baseBlockSize = 50;
		$baseImage = $this->imageManager->canvas(
			$matrix->getBlockCount() * $baseBlockSize,
			$matrix->getBlockCount() * $baseBlockSize,
			$backgroundColor
		);

		for ($rowIndex = 0; $rowIndex < $matrix->getBlockCount(); ++$rowIndex) {
			for ($columnIndex = 0; $columnIndex < $matrix->getBlockCount(); ++$columnIndex) {
				if (1 === $matrix->getBlockValue($rowIndex, $columnIndex)) {
					$baseImage->rectangle(
						$columnIndex * $baseBlockSize,
						$rowIndex * $baseBlockSize,
						($columnIndex + 1) * $baseBlockSize,
						($rowIndex + 1) * $baseBlockSize,
						function (AbstractShape $draw) use ($foregroundColor) {
							$draw->background($foregroundColor);
						}
					);
				}
			}
		}

		$baseImage->resize(
			$matrix->getInnerSize(),
			$matrix->getInnerSize()
		);

		$interpolatedImage = $this->imageManager
			->canvas(
				$matrix->getOuterSize(),
				$matrix->getOuterSize(),
				$backgroundColor
			)
			->insert(
				$baseImage,
				'center',
				$matrix->getMarginLeft(),
				$matrix->getMarginLeft()
			);

		if (PHP_VERSION_ID < 80000) {
			$baseImage->destroy();
		}

		return new CustomResult($interpolatedImage);
	}

	/**
	 * @param \Endroid\QrCode\Color\ColorInterface $color
	 *
	 * @return array
	 */
	private function colorToArray (ColorInterface $color): array {
		return [
			$color->getRed(),
			$color->getGreen(),
			$color->getBlue(),
			$color->getOpacity(),
		];
	}
}
