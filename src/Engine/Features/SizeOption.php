<?php

declare(strict_types=1);

namespace IsaqueSb\Image\Processor\Engine\Features;

class SizeOption
{
	public int $width;

	public string $path;

	public ?int $quality = null;

	public function __construct(int $width, string $path, ?int $quality = 100)
	{
		$this->width = $width;
		$this->path = $path;
		$this->quality = $quality;
	}

	public function withPath(string $path): self
	{
		$this->path = $path;
		return $this;
	}
}
