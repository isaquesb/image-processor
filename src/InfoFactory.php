<?php

declare(strict_types=1);

namespace IsaqueSb\Image\Processor;

class InfoFactory
{
	public function fromContent(string $contents): Info
	{
		$data = getimagesizefromstring($contents);
		if (false === $data) {
			return new Info(IMAGETYPE_UNKNOWN, null, null);
		}
		list($width, $height, $type) = $data;
		return new Info($type, $width, $height, collect($data)->filter(fn($value, $key) => is_string($key))->toArray());
	}
}
