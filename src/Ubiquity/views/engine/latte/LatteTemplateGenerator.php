<?php

namespace Ubiquity\views\engine\latte;

/**
 * Latte template generator.
 * Ubiquity\views\engine\latte$LatteTemplateGenerator
 * This class is part of Ubiquity
 *
 * @author jcheron <myaddressmail@gmail.com>
 * @version 1.0.0
 *
 */
class LatteTemplateGenerator extends \Ubiquity\views\engine\TemplateGenerator {

	private function preProcess(string $code): string {
		$code=\preg_replace('@\|\s?raw@', '|noescape', $code);
		return $code;
	}

	private function postProcess(string $code): string {
		$code=\preg_replace('@\$(.*?)->(.*?)([\s|}]+)@', '\$$1["$2"]$3', $code);
		return \str_replace(['$_self', '$nonce', '$config->siteUrl','{block _'], ['$this->getName()', '$nonce??""', "\$config['siteUrl']",'{block b_'], $code);
	}

	public function parseFromTwig(string $code): string {
		if (\class_exists(\LatteTools\TwigConverter::class)) {
			$code=$this->preProcess($code);
			$converter = new \LatteTools\TwigConverter();
			$code = $converter->convert($code);
			return $this->postProcess($code);
		}
		return '\LatteTools\TwigConverter does not exist!';
	}

}
