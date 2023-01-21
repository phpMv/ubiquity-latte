<?php

namespace Ubiquity\views\engine\latte;

use Ubiquity\utils\base\UArray;
use Ubiquity\utils\base\UString;

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

	public function __construct() {
		$this->openExpressionTag = $this->openVarTag = '{';
		$this->closeExpressionTag = $this->closeVarTag = '}';
		$this->safeFilter = 'noescape';
	}

	public function openBlock(string $name): string {
		return $this->openExpressionTag . "block $name" . $this->closeExpressionTag;
	}

	public function closeBlock(): string {
		return $this->openExpressionTag . '/block' . $this->closeExpressionTag;
	}

	public function asArray(array $array): string {
		return UArray::asPhpArray_($array);
	}

	public function insertVariable(string $name, bool $safe = false): string {
		$filter = '';
		if ($safe) {
			$filter = '|' . $this->safeFilter;
		}
		$name = \str_replace('.', '->', $name);
		if (UString::contains('(', $name)) {
			return $this->openVarTag . $name . $filter . $this->closeVarTag;
		}
		return $this->openVarTag . $this->asVariable($name) . $filter . $this->closeVarTag;
	}

	public function asVariable(string $var): string {
		return '$' . \ltrim($var, '$');
	}

	public function includeFile(string $filename, bool $asVariable = false): string {
		$quote = "'";
		if ($asVariable) {
			$quote = '';
			$filename = $this->asVariable($filename);
		}
		return $this->openExpressionTag . "include {$quote}{$filename}{$quote}" . $this->closeExpressionTag;
	}

	public function extendsTemplate(string $templateName, bool $asVariable = false): string {
		$quote = "'";
		if ($asVariable) {
			$quote = '';
			$templateName = $this->asVariable($templateName);
		}
		return $this->openExpressionTag . "layout {$quote}{$templateName}{$quote}" . $this->closeExpressionTag;
	}

	public function foreach(string $arrayName, string $value, ?string $key = null): string {
		$arrayName = $this->asVariable($arrayName);
		$value = $this->asVariable($value);
		if ($key != null) {
			$key = $this->asVariable($key);
			return $this->openExpressionTag . "foreach $arrayName as $key=>$value" . $this->closeExpressionTag;
		}
		return $this->openExpressionTag . "foreach $arrayName as $value" . $this->closeExpressionTag;
	}

	public function endForeach(): string {
		return $this->openExpressionTag . '/foreach' . $this->closeExpressionTag;
	}

	public function condition(string $condition): string {
		$condition = $this->asVariable(\trim($condition));
		return $this->openExpressionTag . "if $condition" . $this->closeExpressionTag;
	}

	public function endCondition(): string {
		return $this->openExpressionTag . '/if' . $this->closeExpressionTag;
	}

	public function getNonce(): string {
		return $this->openExpressionTag . "\$nonce??''" . $this->closeExpressionTag;
	}

	public function getNonceArray(): string {
		return "['nonce'=>\$nonce??'']";
	}

	public function getSelf(): string {
		return $this->openExpressionTag . '$this->getName()' . $this->closeExpressionTag;
	}

	private function postProcess(string $code): string {
		return \str_replace('$nonce', '$nonce??""', $code);
	}

	public function parseFromTwig(string $code): string {
		if (\class_exists(\LatteTools\TwigConverter::class)) {
			$converter = new \LatteTools\TwigConverter();
			$code = $converter->convert($code);
			return $this->postProcess($code);
		}
		return '\LatteTools\TwigConverter does not exist!';
	}

}
