<?php

declare(strict_types=1);

namespace IsaqueSb\Image\Processor\Engine\Features;

use IsaqueSb\Image\Processor\Image;

interface Resize
{
	/**
	 * @param Image $image
	 * @param SizeOption[] $options
	 * @return Image[]
	 */
	public function resize(Image $image, array $options): array;
}
