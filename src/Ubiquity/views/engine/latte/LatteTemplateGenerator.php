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

	private function replaceSpe(string $code): string {
		return \str_replace(['$_self', '$nonce', '$config->siteUrl'], ['$this->getName()', '$nonce??""', "\$config['siteUrl']"], $code);
	}

	public function parseFromTwig(string $code): string {
		if (\class_exists(\LatteTools\TwigConverter::class)) {
			$converter = new \LatteTools\TwigConverter();
			$code = $converter->convert($code);
			return $this->replaceSpe($code);
		}
		return '\LatteTools\TwigConverter does not exist!';
	}

}
