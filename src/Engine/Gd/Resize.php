<?php

declare(strict_types=1);

namespace IsaqueSb\Image\Processor\Engine\Gd;

use IsaqueSb\Image\Processor\Engine\Features;
use IsaqueSb\Image\Processor\Image;

class Resize implements Features\Resize
{
	use CreateImage;

	/**
	 * @param Image $image
	 * @param Features\SizeOption[] $options
	 * @return Image[]
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	public function resize(Image $image, array $options): array
	{
		$source = imagecreatefromstring($image->getContent());
		$resizes = collect($options)
			->map(fn (Features\SizeOption $option) => $this->resizeImage(
				$image,
				$source,
				$option,
				$image->getInfo()->getType(),
			));
		imagedestroy($source);
		return $resizes->toArray();
	}
}
