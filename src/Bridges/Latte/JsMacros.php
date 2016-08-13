<?php

namespace Sw2\LoadJs\Bridges\Latte;

use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;

/**
 * Class JsMacros
 *
 * @package Sw2\JsLoad
 */
class JsMacros extends MacroSet
{

	/**
	 * @param Compiler $compiler
	 *
	 * @return static
	 */
	public static function install(Compiler $compiler)
	{
		$me = new static($compiler);
		$me->addMacro('js', [$me, 'macroJs'], NULL, [$me, 'macroAttrJs']);

		return $me;
	}

	/**
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 */
	public function macroJs(MacroNode $node, PhpWriter $writer)
	{
		return $writer->write("echo %escape(\$basePath . '/' . \$presenter->context->getService('sw2load.compiler.js')->link(%node.word));");
	}

	/**
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 */
	public function macroAttrJs(MacroNode $node, PhpWriter $writer)
	{
		return $writer->write("echo ' src=\"' . %escape(\$basePath . '/' . \$presenter->context->getService('sw2load.compiler.js')->link(%node.word)) . '\"';");
	}

}
