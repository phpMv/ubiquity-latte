<?php

namespace Ubiquity\views\engine\latte;

/**
 * Uniquity Latte File loader.
 * Ubiquity\views\engine\latteULatteFileLoader
 * This class is part of Ubiquity
 *
 * @author jcheron <myaddressmail@gmail.com>
 * @version 1.0.0
 *
 */
class ULatteFileLoader extends \Latte\Loaders\FileLoader {

	private array $namespaces = [];

	private function getFilename(string $fileName): string {
		if (\preg_match('/\@(.*?)\//', $fileName, $match) == 1) {
			$ns = '@' . $match[1];
			if ($dir = $this->namespaces[$ns] ?? false) {
				return \realpath(\str_replace($ns, $dir, $fileName));
			}
		}
		return $this->baseDir . $fileName;
	}

	public function addPath($path, $namespace): void {
		$this->namespaces['@' . \ltrim($namespace, '@')] = $path;
	}

	public function getContent(string $fileName): string {
		$file = $this->getFilename($fileName);
		return \file_get_contents($file);
	}

	public function isExpired(string $file, int $time): bool {
		$file = $this->getFilename($file);
		if (\file_exists($file)) {
			$mtime = \filemtime($file);
			return !$mtime || $mtime > $time;
		}
		return true;
	}

	/**
	 * Returns unique identifier for caching.
	 */
	public function getUniqueId(string $file): string {
		return $this->getFilename($file);
	}

	public function getReferredName(string $file, string $referringFile): string {
		return $file;
	}
}
