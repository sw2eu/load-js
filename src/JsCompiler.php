<?php

namespace Sw2\LoadJs;

use JSMin;
use Nette\Caching\Cache;
use Nette\Utils\Strings;
use Sw2\Load\DI\LoadExtension;
use Sw2\Load\ICompiler;

/**
 * Class JsCompiler
 *
 * @package Sw2\JsLoad
 */
class JsCompiler implements ICompiler
{
	/** @var Cache */
	private $cache;

	/** @var bool */
	private $debugMode;

	/** @var string */
	private $wwwDir;

	/** @var string */
	private $genDir;

	/** @var array */
	private $files;

	/** @var array */
	private $statistics = [];

	/**
	 * @param Cache $cache
	 * @param bool $debugMode
	 * @param string $wwwDir
	 * @param string $genDir
	 * @param array $files
	 */
	public function __construct(Cache $cache, $debugMode, $wwwDir, $genDir, array $files)
	{
		$this->cache = $cache;
		$this->debugMode = $debugMode;
		$this->wwwDir = $wwwDir;
		$this->genDir = $genDir;
		$this->files = $files;
	}

	/**
	 * @return array
	 */
	public function getStatistics()
	{
		return $this->statistics;
	}

	/**
	 * @param string $name
	 *
	 * @return string
	 */
	public function link($name)
	{
		$path = $this->cache->load([$name, $this->debugMode]);
		$files = $this->files[$name];
		$files = is_array($files) ? $files : [$files];

		if ($path === NULL) {
			$time = self::maxTime($files);
			$hash = LoadExtension::computeHash($files, $time, $this->debugMode);
			$path = "{$this->genDir}/$name-$hash.js";
			$this->cache->save([$name, $this->debugMode], $path);
		}

		$genFile = "{$this->wwwDir}/$path";
		if (!file_exists($genFile) || ($this->debugMode && filemtime($genFile) < (isset($time) ? $time : ($time = self::maxTime($files))))) {
			$start = microtime(TRUE);
			$code = '';
			foreach ($files as $file) {
				$fileCode = rtrim(file_get_contents($file), " \t\n\r\0\x0B;");
				$code .= (Strings::endsWith($file, '.min.js') ? $fileCode : JSMin::minify($fileCode)) . ";\n";
			}
			file_put_contents($genFile, $code);
			if ($this->debugMode) {
				$this->statistics[$name]['time'] = microtime(TRUE) - $start;
				$this->statistics[$name]['parsedFiles'] = $files;
			}
		}
		if ($this->debugMode) {
			$this->statistics[$name]['size'] = filesize($genFile);
			$this->statistics[$name]['file'] = count($files) > 1 ? $files : reset($files);
			$this->statistics[$name]['date'] = isset($time) ? $time : ($time = self::maxTime($files));
			$this->statistics[$name]['path'] = $path;
		}

		return $path;
	}

	/**
	 * @param array $files
	 *
	 * @return int
	 */
	private static function maxTime(array $files)
	{
		$time = 0;
		foreach ($files as $file) {
			$time = max($time, filemtime($file));
		}

		return $time;
	}

}
