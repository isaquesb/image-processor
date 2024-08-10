<?php

declare(strict_types=1);

namespace IsaqueSb\Image\Processor;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

class Image
{
	public ?string $disk;

	public string $path;

	public ?Info $info = null;

	public function __construct(?string $disk, string $path, ?Info $info = null)
	{
		$this->disk = $disk;
		$this->path = $path;
		$this->info = $info;
	}

	/**
	 * @throws FileNotFoundException
	 */
	public function getInfo(): Info
	{
		if (null === $this->info) {
			$this->info = (new InfoFactory())->fromContent($this->getContent());
		}
		return $this->info;
	}

	public function isValid(): bool
	{
		try {
			$this->getInfo();
			return !$this->info->is(IMAGETYPE_UNKNOWN);
		} catch (FileNotFoundException $e) {
			return false;
		}
	}

	/**
	 * @throws FileNotFoundException
	 */
	public function getContent(): string
	{
		return $this->getDisk()->get($this->path);
	}

	public function getDisk(): \Illuminate\Contracts\Filesystem\Filesystem
	{
		return app('filesystem')->disk($this->disk);
	}
}
