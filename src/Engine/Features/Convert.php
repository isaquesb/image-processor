<?php

declare(strict_types=1);

namespace IsaqueSb\Image\Processor\Engine\Features;

use IsaqueSb\Image\Processor\Image;

interface Convert
{
	public function convert(Image $image, int $type, SizeOption $sizeOption): Image;
}
