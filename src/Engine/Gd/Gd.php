<?php

declare(strict_types=1);

namespace IsaqueSb\Image\Processor\Engine\Gd;

use IsaqueSb\Image\Processor\Engine\Engine;
use IsaqueSb\Image\Processor\Engine\Features;
use IsaqueSb\Image\Processor\Image;

class Gd implements Engine, Features\Resize, Features\Convert
{
	public function name(): string
	{
		return 'Gd';
	}

	public function installed(): bool
	{
		return extension_loaded('gd');
	}

	public function supportFormats(): array
	{
		$gdInfo = gd_info();
		$formats = [];
		foreach ($gdInfo as $key => $value) {
			if (!is_bool($value) || !$value) {
				continue;
			}
			$formatKey = strtolower(explode(' ', $key)[0]);
			$formats[] = $formatKey;
		}
		return array_values(array_unique($formats));
	}

	/**
	 * @param Image $image
	 * @param Features\SizeOption[] $options
	 * @return Image[]
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	public function resize(Image $image, array $options): array
	{
		return (new Resize())->resize($image, $options);
	}

	public function convert(Image $image, int $type, Features\SizeOption $sizeOption): Image
	{
		return (new Conversor())->convert($image, $type, $sizeOption);
	}
}
