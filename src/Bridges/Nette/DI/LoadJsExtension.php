<?php

namespace Sw2\LoadJs\Bridges\Nette\DI;

use Sw2\Load\Bridges\Nette\DI\LoadExtension;
use Sw2\LoadJs\Bridges\Latte\JsMacros;
use Sw2\LoadJs\Compiler\JsCompiler;

/**
 * Class JsLoadExtension
 *
 * @package Sw2\JsLoad
 */
class LoadJsExtension extends LoadExtension
{

	public function beforeCompile()
	{
		$this->addCompilerDefinition('js', JsCompiler::class);
		$this->registerMacros(JsMacros::class);
		$this->registerDebugger();
	}

}
