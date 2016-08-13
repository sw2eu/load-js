<?php

namespace Sw2\LoadJs\Compiler;

use JSMin;
use Nette\Utils\Strings;
use Sw2\Load\Compiler\BaseCompiler;
use Sw2\Load\Helpers;

/**
 * Class JsCompiler
 *
 * @package Sw2\JsLoad
 */
class JsCompiler extends BaseCompiler
{

	/**
	 * @param array $sourceFiles
	 * @return int
	 */
	protected function getModifyTime($sourceFiles)
	{
		$time = 0;
		foreach ($sourceFiles as $sourceFile) {
			$time = max($time, filemtime($sourceFile));
		}
		return $time;
	}

	/**
	 * @param string $name
	 * @param array $files
	 * @param int $time
	 * @param bool $debugMode
	 * @return string
	 */
	protected function getOutputFilename($name, $files, $time, $debugMode)
	{
		return $name . '-' . Helpers::computeHash($files, $time, $debugMode) . '.js';
	}

	/**
	 * @param array $sourceFiles
	 * @param string $outputFile
	 * @return array
	 */
	protected function compile($sourceFiles, $outputFile)
	{
		$file = fopen($outputFile, 'w');
		foreach ($sourceFiles as $sourceFile) {
			$fileCode = rtrim(file_get_contents($sourceFile), " \t\n\r\0\x0B;");
			fwrite($file, (Strings::endsWith($sourceFile, '.min.js') ? $fileCode : JSMin::minify($fileCode)) . ";\n");
			fflush($file);
		}
		fclose($file);
		return $sourceFiles;
	}

}
