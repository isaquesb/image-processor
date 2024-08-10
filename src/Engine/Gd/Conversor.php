<?php

declare(strict_types=1);

namespace IsaqueSb\Image\Processor\Engine\Gd;

use IsaqueSb\Image\Processor\Engine\Features;
use IsaqueSb\Image\Processor\Image;

class Conversor implements Features\Convert
{
	use CreateImage;

	public function convert(Image $image, int $type, Features\SizeOption $sizeOption): Image
	{
		$source = imagecreatefromstring($image->getContent());
		$image = $this->resizeImage($image, $source, $sizeOption, $type);
		imagedestroy($source);
		return $image;
	}
}
