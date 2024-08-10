<?php

declare(strict_types=1);

namespace IsaqueSb\Image\Processor\Engine\Imagick;

use IsaqueSb\Image\Processor\Engine\Engine;
use IsaqueSb\Image\Processor\Engine\Features;
use IsaqueSb\Image\Processor\Engine\Features\SizeOption;
use IsaqueSb\Image\Processor\Image;
use IsaqueSb\Image\Processor\Info;
use Imagick as ImagickLib;

class Imagick implements Engine, Features\Resize, Features\Convert
{
	public function name(): string
	{
		return 'Imagick';
	}

	public function installed(): bool
	{
		return extension_loaded('imagick');
	}

	public function supportFormats(): array
	{
		return collect(ImagickLib::queryFormats())->map(fn ($format) => strtolower($format))->toArray();
	}

	public function convert(Image $image, int $type, SizeOption $sizeOption): Image
	{
		$info = $image->getInfo();
		$imagick = new ImagickLib();
		$imagick->readImageBlob($image->getContent());
		$imagick->setImageFormat(image_type_to_extension($type, false));
		$sizeOption->quality && $imagick->setImageCompressionQuality($sizeOption->quality);
		$image->getDisk()->put($sizeOption->path, $imagick->getImageBlob());
		$info = new Info($type, $info->getWidth(), $info->getHeight(), [
			'original' => [
				'path' => $image->path,
				'info' => $info->toArray(),
			],
		]);
		return new Image($image->disk, $sizeOption->path, $info);
	}

	public function resize(Image $image, array $options): array
	{
		$source = new ImagickLib();
		$source->readImageBlob($image->getContent());
		$resizes = collect($options)
			->map(fn (Features\SizeOption $option) =>  $this->resizeImage(
				$image,
				clone($source),
				$option,
			));
		$source->destroy();
		return $resizes->toArray();
	}

	private function resizeImage(Image $image, ImagickLib $source, Features\SizeOption $option): Image
	{
		$info = $image->getInfo();
		$source->scaleImage($option->width, 0);
		$source->setImageCompression(ImagickLib::COMPRESSION_JPEG);
		$option->quality && $source->setImageCompressionQuality($option->quality);
		$image->getDisk()->put($option->path, $source->getImageBlob());
		$info = new Info($info->getType(), $source->getImageWidth(), $source->getImageHeight(), [
			'original' => [
				'path' => $image->path,
				'info' => $info->toArray(),
			],
		]);
		$source->destroy();
		return new Image($image->disk, $option->path, $info);
	}
}
