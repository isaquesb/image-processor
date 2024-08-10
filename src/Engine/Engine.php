<?php

declare(strict_types=1);

namespace IsaqueSb\Image\Processor\Engine;

interface Engine
{
	public function name(): string;

	public function installed(): bool;

	public function supportFormats(): array;
}
