<?php

declare(strict_types=1);

namespace IsaqueSb\Image\Processor;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Info implements Arrayable, \JsonSerializable
{
	protected int $type = IMAGETYPE_UNKNOWN;

	protected ?int $width = null;

	protected ?int $height = null;

	protected array $attributes = [];

	public function __construct(int $type, ?int $width, ?int $height, array $attributes = [])
	{
		$this->type = $type;
		$this->width = $width;
		$this->height = $height;
		$this->attributes = $attributes;
	}

	public function getMimeType(): ?string
	{
		if (IMAGETYPE_UNKNOWN === $this->type) {
			return null;
		}
		return image_type_to_mime_type($this->type);
	}

	public function getExtension(bool $includeDot = false): ?string
	{
		if (IMAGETYPE_UNKNOWN === $this->type) {
			return null;
		}
		return image_type_to_extension($this->type, $includeDot);
	}

	public function is(int $type): bool
	{
		return $this->type === $type;
	}

	public function getType(): int
	{
		return $this->type;
	}

	public function getWidth(): ?int
	{
		return $this->width;
	}

	public function getHeight(): ?int
	{
		return $this->height;
	}

	public function getAttributes(): array
	{
		return $this->attributes;
	}

	public function toArray(): array
	{
		return [
			'type' => $this->type,
			'extension' => $this->getExtension(),
			'mime' => $this->getMimeType(),
			'width' => $this->width,
			'height' => $this->height,
			'attributes' => $this->attributes,
		];
	}

	public function jsonSerialize()
	{
		return $this->toArray();
	}
}
