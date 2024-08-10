<?php

declare(strict_types=1);

namespace IsaqueSb\Image\Processor\Engine\Gd;

use IsaqueSb\Image\Processor\Engine\Features;
use IsaqueSb\Image\Processor\Image;
use IsaqueSb\Image\Processor\Info;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

trait CreateImage
{
	private function imageToString($image, int $type, Features\SizeOption $options): string
	{
		ob_start();
		switch ($type) {
			case IMAGETYPE_JPEG:
				imagejpeg($image, null, $options->quality ?? -1);
				break;
			case IMAGETYPE_PNG:
				imagepng($image, null, $options->quality ?? -1);
				break;
			case IMAGETYPE_GIF:
				imagegif($image);
				break;
		}
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	/**
	 * @param Image $image
	 * @param resource $source
	 * @param Features\SizeOption $option
	 * @param int $type
	 * @return Image
	 * @throws FileNotFoundException
	 */
	private function resizeImage(Image $image, $source, Features\SizeOption $option, int $type): Image
	{
		$width = imagesx($source);
		$height = imagesy($source);
		$info = $image->getInfo();
		$percent = min($width, $option->width) / $width;
		$newWidth = (int) round($width * $percent);
		$newHeight = (int) round($height * $percent);
		$newImage = imagecreatetruecolor($newWidth, $newHeight);
		if ($info->is(IMAGETYPE_PNG) || $info->is(IMAGETYPE_GIF)) {
			imagealphablending($newImage, false);
			imagesavealpha($newImage, true);
		}
		imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
		$newContent = $this->imageToString($newImage, $type, $option);
		imagedestroy($newImage);
		$image->getDisk()->put($option->path, $newContent);
		$info = new Info($type, $newWidth, $newHeight, [
			'original' => [
				'path' => $image->path,
				'info' => $info->toArray(),
			],
		]);
		return new Image($image->disk, $option->path, $info);
	}
}
