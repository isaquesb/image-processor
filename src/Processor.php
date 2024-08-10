<?php

declare(strict_types=1);

namespace IsaqueSb\Image\Processor;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Str;

class Processor
{
	/**
	 * @var array<string, Engine\Engine>
	 */
	protected array $engines = [];

	protected string $defaultEngine;

	public function __construct(array $engines, string $defaultEngine)
	{
		$this->engines = $engines;
		$this->defaultEngine = $defaultEngine;
	}

	public function setDefaultEngine(string $defaultEngine): void
	{
		$this->defaultEngine = $defaultEngine;
	}

	public function getEngines(): array
	{
		return $this->engines;
	}

	public function getEngine(): Engine\Engine
	{
		$engine = $this->engines[$this->defaultEngine] ?? reset($this->engines);
		if (!$engine) {
			throw new ImageProcessException('No image engine installed');
		}
		return $engine;
	}

	/**
	 * @throws ImageProcessException
	 */
	public function getEngineWithFeature(string $featureInterface): Engine\Engine
	{
		$default = $this->getEngine();
		if ($default instanceof $featureInterface) {
			return $default;
		}
		$engine = collect($this->engines)
			->first(fn (Engine\Engine $engine) => $engine instanceof $featureInterface);
		if (!$engine) {
			throw new ImageProcessException('No engine installed with feature ' . $featureInterface);
		}
		return $engine;
	}

	/**
	 * @throws ImageProcessException
	 * @throws FileNotFoundException
	 */
	public function convert(Image $image, int $type, Engine\Features\SizeOption $sizeOption): Image
	{
		$info = $image->getInfo();
		if ($info->is($type)) {
			return $image;
		}
		$path = $sizeOption->path;
		if (!Str::endsWith($path, $info->getExtension(true))) {
			$path .= $info->getExtension(true);
		}
		$newExtension = image_type_to_extension($type);
		if (!Str::endsWith($path, $newExtension)) {
			$path = Str::replaceLast($info->getExtension(true), $newExtension, $path);
		}
		$sizeOption->withPath($path);
		return $this->getEngineWithFeature(Engine\Features\Convert::class)
			->convert($image, $type, $sizeOption);
	}

	/**
	 * @throws ImageProcessException
	 * @throws FileNotFoundException
	 */
	public function resize(Image $image, array $options): array
	{
		$optionsWithExtension = collect($options)
			->map(function (Engine\Features\SizeOption $option) use ($image) {
				$newPath = $option->path;
				$newExtension = pathinfo($image->path, PATHINFO_EXTENSION);
				if (!Str::endsWith($newPath, $newExtension)) {
					$newPath .= '.' . $newExtension;
				}
				$option->withPath($newPath);
				return $option;
			})
			->toArray();
		return $this->getEngineWithFeature(Engine\Features\Resize::class)
			->resize($image, $optionsWithExtension);
	}
}
