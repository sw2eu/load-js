<?php

namespace Sw2\LoadJs\DI;

use Nette;
use Sw2\Load\DI\LoadExtension;
use Sw2\LoadJs\JsCompiler;
use Sw2\LoadJs\JsMacros;

/**
 * Class JsLoadExtension
 *
 * @package Sw2\JsLoad
 */
class LoadJsExtension extends LoadExtension
{
	/** @var array */
	public $defaults = [
		'debugger' => FALSE,
		'genDir' => 'webtemp',
		'files' => [],
	];

	/**
	 * Processes configuration data. Intended to be overridden by descendant.
	 */
	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);
		$wwwDir = $builder->parameters['wwwDir'];
		$genDir = $config['genDir'];

		if (!is_writable("$wwwDir/$genDir")) {
			throw new Nette\IOException("Directory '$wwwDir/$genDir' is not writable.");
		}

		$args = [$builder->parameters['debugMode'], $wwwDir, $genDir, $config['files']];
		$this->addCompilerDefinition('js', JsCompiler::class, $args);
		$this->registerMacros(JsMacros::class);
		$this->registerDebugger($config['debugger']);
	}

}
