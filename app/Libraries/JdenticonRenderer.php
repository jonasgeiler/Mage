<?php

namespace Libraries;

use Intervention\Image\AbstractShape;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Jdenticon\Color;
use Jdenticon\Rendering\Point;
use Jdenticon\Rendering\RendererInterface;
use Jdenticon\Rendering\Transform;

/**
 * Custom Renderer for Jdenticon that uses `intervention/image` in order to export to multiple formats
 */
class JdenticonRenderer implements RendererInterface {

	/**
	 * @var \Jdenticon\Rendering\Transform Current transform
	 */
	private Transform $transform;

	/**
	 * @var \Intervention\Image\ImageManager
	 */
	private ImageManager $imageManager;

	/**
	 * @var int Image size
	 */
	private int $size;

	/**
	 * @var \Jdenticon\Color Image background color
	 */
	private $backgroundColor;

	/**
	 * @var array All shapes
	 */
	private array $shapes = [];

	/**
	 * @var \Jdenticon\Color Color of current shape
	 */
	private $shapeColor;

	/**
	 * Creates an instance of the class JdenticonRenderer.
	 *
	 * @param int                              $size Size of the image
	 * @param \Intervention\Image\ImageManager $imageManager
	 */
	public function __construct (int $size, ImageManager $imageManager) {
		$this->transform = Transform::getEmpty();
		$this->size = $size;
		$this->imageManager = $imageManager;
	}

	/**
	 * Draws the image
	 *
	 * @return \Intervention\Image\Image
	 */
	public function getImage (): Image {
		$backgroundColor = $this->colorToArray($this->backgroundColor);

		$image = $this->imageManager->canvas(
			$this->size,
			$this->size,
			$backgroundColor
		);

		foreach ($this->shapes as $shape) {
			$shapeColor = $shape['invert']
				? $backgroundColor
				: $this->colorToArray($shape['color']);

			if ($shape['type'] === 'polygon') {
				$image->polygon(
					$shape['points'],
					function (AbstractShape $draw) use ($shapeColor) {
						$draw->background($shapeColor);
					}
				);
			} elseif ($shape['type'] === 'circle') {
				$image->circle(
					$shape['size'],
					$shape['x'],
					$shape['y'],
					function (AbstractShape $draw) use ($shapeColor) {
						$draw->background($shapeColor);
					}
				);
			}
		}

		return $image;
	}

	/**
	 * @param \Jdenticon\Color $color
	 *
	 * @return array
	 */
	private function colorToArray (Color $color): array {
		return [
			$color->r,
			$color->g,
			$color->b,
			round($color->a / 255, 2),
		];
	}

	/**
	 * Gets the output from the renderer.
	 *
	 * @return string
	 */
	public function getData (): string {
		return (string) $this->getImage()->encode('png');
	}

	/**
	 * Begins a new shape. The shape should be ended with a call to endShape.
	 *
	 * @param \Jdenticon\Color $color The color of the shape.
	 */
	public function beginShape (Color $color): void {
		$this->shapeColor = $color;
	}

	/**
	 * Adds a circle to the image.
	 *
	 * @param float $x      The x-coordinate of the bounding rectangle
	 *                      upper-left corner.
	 * @param float $y      The y-coordinate of the bounding rectangle
	 *                      upper-left corner.
	 * @param float $size   The size of the bounding rectangle.
	 * @param bool  $invert If true the area of the circle will be removed
	 *                      from the filled area.
	 */
	public function addCircle ($x, $y, $size, $invert = false): void {
		$transformedPoint = $this->transform->transformPoint($x, $y, $size, $size);

		$this->shapes[] = [
			'type'   => 'circle',
			'color'  => $this->shapeColor,
			'x'      => $transformedPoint->x + $size / 2,
			'y'      => $transformedPoint->y + $size / 2,
			'size'   => $size,
			'invert' => $invert,
		];
	}

	/**
	 * @param array $points
	 * @param bool  $invert If true the area of the polygon will be removed from the filled area.
	 */
	private function addPolygonCore (array $points, bool $invert): void {
		$transformedPoints = [];

		foreach ($points as $point) {
			$transformedPoint = $this->transform->transformPoint($point->x, $point->y);
			$transformedPoints[] = $transformedPoint->x;
			$transformedPoints[] = $transformedPoint->y;
		}

		$this->shapes[] = [
			'type'   => 'polygon',
			'color'  => $this->shapeColor,
			'points' => $transformedPoints,
			'invert' => $invert,
		];
	}

	/**
	 * Adds a rectangle to the image.
	 *
	 * @param float $x      The x-coordinate of the rectangle upper-left corner.
	 * @param float $y      The y-coordinate of the rectangle upper-left corner.
	 * @param float $width  The width of the rectangle.
	 * @param float $height The height of the rectangle.
	 * @param bool  $invert If true the area of the rectangle will be removed
	 *                      from the filled area.
	 */
	public function addRectangle ($x, $y, $width, $height, $invert = false): void {
		$this->addPolygonCore([
			new Point($x, $y),
			new Point($x + $width, $y),
			new Point($x + $width, $y + $height),
			new Point($x, $y + $height),
		], $invert);
	}

	/**
	 * Adds a polygon to the image.
	 *
	 * @param array $points Array of points that the polygon consists of.
	 * @param bool  $invert If true the area of the polygon will be removed
	 *                      from the filled area.
	 */
	public function addPolygon ($points, $invert = false): void {
		$this->addPolygonCore($points, $invert);
	}

	/**
	 * Adds a triangle to the image.
	 *
	 * @param float $x         The x-coordinate of the bounding rectangle
	 *                         upper-left corner.
	 * @param float $y         The y-coordinate of the bounding rectangle
	 *                         upper-left corner.
	 * @param float $width     The width of the bounding rectangle.
	 * @param float $height    The height of the bounding rectangle.
	 * @param float $direction The direction of the 90 degree corner of the
	 *                         triangle.
	 * @param bool  $invert    If true the area of the triangle will be removed
	 *                         from the filled area.
	 */
	public function addTriangle ($x, $y, $width, $height, $direction, $invert = false): void {
		$points = [
			new Point($x + $width, $y),
			new Point($x + $width, $y + $height),
			new Point($x, $y + $height),
			new Point($x, $y),
		];

		array_splice($points, $direction, 1);

		$this->addPolygonCore($points, $invert);
	}

	/**
	 * Adds a rhombus to the image.
	 *
	 * @param float $x      The x-coordinate of the bounding rectangle
	 *                      upper-left corner.
	 * @param float $y      The y-coordinate of the bounding rectangle
	 *                      upper-left corner.
	 * @param float $width  The width of the bounding rectangle.
	 * @param float $height The height of the bounding rectangle.
	 * @param bool  $invert If true the area of the rhombus will be removed
	 *                      from the filled area.
	 */
	public function addRhombus ($x, $y, $width, $height, $invert = false): void {
		$this->addPolygonCore([
			new Point($x + $width / 2, $y),
			new Point($x + $width, $y + $height / 2),
			new Point($x + $width / 2, $y + $height),
			new Point($x, $y + $height / 2),
		], $invert);
	}

	/**
	 * Ends the currently drawn shape.
	 */
	public function endShape (): void {
	}

	/**
	 * Gets the MIME type of the renderer output.
	 *
	 * @return string
	 */
	public function getMimeType (): string {
		return 'image/png';
	}

	/**
	 * Sets the current transform that will be applied on all coordinates before
	 * being rendered to the target image.
	 *
	 * @param \Jdenticon\Rendering\Transform $transform The transform to set.
	 *                                                  If NULL is specified any existing transform is removed.
	 */
	public function setTransform (Transform $transform): void {
		$this->transform = $transform ?? Transform::getEmpty();
	}

	/**
	 * Gets the current transform that will be applied on all coordinates before
	 * being rendered to the target image.
	 *
	 * @return \Jdenticon\Rendering\Transform
	 */
	public function getTransform (): Transform {
		return $this->transform;
	}

	/**
	 * Sets the background color of the image.
	 *
	 * @param \Jdenticon\Color $color The image background color.
	 */
	public function setBackgroundColor (Color $color): void {
		$this->backgroundColor = $color;
	}

	/**
	 * Gets the background color of the image.
	 *
	 * @return \Jdenticon\Color
	 */
	public function getBackgroundColor (): Color {
		return $this->backgroundColor;
	}
}
