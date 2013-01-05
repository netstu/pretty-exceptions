<?php

/*
  +------------------------------------------------------------------------+
  | Phalcon Framework                                                      |
  +------------------------------------------------------------------------+
  | Copyright (c) 2011-2013 Phalcon Team (http://www.phalconphp.com)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file docs/LICENSE.txt.                        |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Authors: Andres Gutierrez <andres@phalconphp.com>                      |
  |          Eduar Carvajal <eduar@phalconphp.com>                         |
  +------------------------------------------------------------------------+
*/

namespace Phalcon;

/**
 * Phalcon\BeutyExceptions
 *
 * Prints exception/errors backtraces using a pretty visualization
 */
class BeutyExceptions
{

	/**
	 * Show the application's code
	 */
	protected $_showFiles = true;

	/**
	 * Show only the related part of the application
	 */
	protected $_showFileFragment = false;

	/**
	 * CSS theme
	 */
	protected $_theme = 'default';

	/**
	 * Flag to control that only one exception/error is show at time
	 */
	static protected $_showActive = false;

	/**
	 * Set if the application's files must be opened an showed as part of the backtrace
	 *
	 * @param boolean $showFiles
	 */
	public function setShowFiles($showFiles)
	{
		$this->_showFiles = $showFiles;
	}

	/**
	 * Set if only the file fragment related to the exception must be shown instead of the complete file
	 *
	 * @param boolean $showFileFragment
	 */
	public function showFileFragment($showFileFragment)
	{
		$this->_showFileFragment = $showFileFragment;
	}

	/**
	 * Returns the css sources
	 *
	 * @return string
	 */
	public function getCssSources()
	{
		return '<link href="/pretty-exceptions/themes/'.$this->_theme.'.css" type="text/css" rel="stylesheet" />';
	}

	/**
	 * Returns the javascript sources
	 *
	 * @return string
	 */
	public function getJsSources()
	{
		return '
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
		<script type="text/javascript" src="/pretty-exceptions/prettify/prettify.js"></script>
		<script type="text/javascript" src="/pretty-exceptions/js/pretty.js"></script>
		<script type="text/javascript" src="/pretty-exceptions/js/jquery.scrollTo-min.js"></script>';
	}

	/**
	 * Handles exceptions
	 *
	 * @param Exception $e
	 * @return boolean
	 */
	public function handle($e)
	{

		@ob_end_clean();

		if (self::$_showActive) {
			echo $e->getMessage();
			return;
		}

		self::$_showActive = true;

		echo '<html><head><title>Exception - ', get_class($e), ': ', $e->getMessage(), '</title>'.$this->getCssSources().'</head><body>';

		echo '<div class="error-main">', get_class($e), ': ', $e->getMessage(), '</div>';

		echo '<div class="error-backtrace"><table cellspacing="0">';
		foreach ($e->getTrace() as $n => $trace) {

			echo '<tr><td align="right" valign="top" class="error-number">#', $n, '</td><td>';
			if (isset($trace['class'])) {
				if (preg_match('/Phalcon/', $trace['class'])) {
					echo '<span class="error-class"><a target="_new" href="http://docs.phalconphp.com/en/latest/api/', str_replace('\\', '_', $trace['class']), '.html">', $trace['class'], '</a></span>', $trace['type'];
				} else {
					echo '<span class="error-class">', $trace['class'], '</span>', $trace['type'];
				}
			}

			echo '<span class="error-function">', $trace['function'], '</span>';
			if (isset($trace['args'])) {
				$arguments = array();
				foreach ($trace['args'] as $argument) {
					if (is_scalar($argument)) {
						$arguments[] = '<span class="error-parameter">'.$argument.'</span>';
					} else {
						if (is_object($argument)) {
							$arguments[] = '<span class="error-parameter">Object(' . get_class($argument) . ')</span>';
						}
					}
				}
				echo '('.join(', ', $arguments).')';
			}
			if (isset($trace['file'])) {
				echo '<br/><span class="error-file">', $trace['file'], ' (', $trace['line'], ')</span>';
			}
			echo '</td></tr>';

			if ($this->_showFiles) {
				if (isset($trace['file'])) {

					echo '</table>';

					$line = $trace['line'];
					$lines = file($trace['file']);

					if ($this->_showFileFragment) {
						$numberLines = count($lines);
						$firstLine = ($line - 7) < 1 ? 1 : $line - 7;
						$lastLine = ($line + 5 > $numberLines ? $numberLines : $line + 5);
						echo "<pre class='prettyprint highlight:".$firstLine.":".$line." linenums:".$firstLine."'>";
					} else {
						$firstLine = 1;
						$lastLine = count($lines) - 1;
						echo "<pre class='prettyprint highlight:".$firstLine.":".$line." linenums error-scroll'>";
					}

					for ($i = $firstLine; $i <= $lastLine; ++$i) {

						if ($this->_showFileFragment) {
							if ($i == $firstLine) {
								if (preg_match('#\*\/$#', rtrim($lines[$i - 1]))) {
									$lines[$i-1] = str_replace("* /", "  ", $lines[$i - 1]);
								}
							}
						}

						if ($lines[$i - 1] != PHP_EOL) {
							$lines[$i - 1] = str_replace("\t", "  ", $lines[$i - 1]);
							echo htmlentities($lines[$i - 1], ENT_COMPAT, 'UTF-8');
						} else {
							echo '&nbsp;'."\n";
						}
					}
					echo '</pre>';

					echo '<table cellspacing="0">';
				}
			}
		}
		echo '</table></div>

		<div class="version">
			Phalcon Framework '.Version::get().'
		</div>

		';

		echo $this->getJsSources().'</body></html>';

		self::$_showActive = false;

		return true;
	}

	/**
	 * Handles errors/warnings/notices
	 *
	 * @param int $errorCode
	 * @param string $errorMessage
	 * @param string $errorFile
	 * @param int $errorLine
	 */
	public function handleError($errorCode, $errorMessage, $errorFile, $errorLine)
	{

		@ob_end_clean();

		if (self::$_showActive) {
			echo $errorMessage;
			return;
		}

		self::$_showActive = true;

		echo '<html><head><title>Exception - ', $errorMessage, '</title>'.$this->getCssSources().'</head><body>';

		echo '<div class="error-main">', $errorMessage, '</div>';

		echo '<div class="error-backtrace"><table cellspacing="0">';
		foreach (debug_backtrace() as $n => $trace) {

			if ($n == 0) {
				continue;
			}

			echo '<tr><td align="right" valign="top" class="error-number">#', $n, '</td><td>';
			if (isset($trace['class'])) {
				if (preg_match('/Phalcon/', $trace['class'])) {
					echo '<span class="error-class"><a target="_new" href="http://docs.phalconphp.com/en/latest/api/', str_replace('\\', '_', $trace['class']), '.html">', $trace['class'], '</a></span>', $trace['type'];
				} else {
					echo '<span class="error-class">', $trace['class'], '</span>', $trace['type'];
				}
			}

			echo '<span class="error-function">', $trace['function'], '</span>';
			if (isset($trace['args'])) {
				$arguments = array();
				foreach ($trace['args'] as $argument) {
					if (is_scalar($argument)) {
						$arguments[] = '<span class="error-parameter">'.$argument.'</span>';
					} else {
						if (is_object($argument)) {
							$arguments[] = '<span class="error-parameter">Object(' . get_class($argument) . ')</span>';
						}
					}
				}
				echo '('.join(', ', $arguments).')';
			}
			if (isset($trace['file'])) {
				echo '<br/><span class="error-file">', $trace['file'], ' (', $trace['line'], ')</span>';
			}
			echo '</td></tr>';

			if ($this->_showFiles) {
				if (isset($trace['file'])) {

					echo '</table>';

					$line = $trace['line'];
					$lines = file($trace['file']);

					if ($this->_showFileFragment) {
						$numberLines = count($lines);
						$firstLine = ($line - 7) < 1 ? 1 : $line - 7;
						$lastLine = ($line + 5 > $numberLines ? $numberLines : $line + 5);
						echo "<pre class='prettyprint highlight:".$firstLine.":".$line." linenums:".$firstLine."'>";
					} else {
						$firstLine = 1;
						$lastLine = count($lines)-1;
						echo "<pre class='prettyprint highlight:".$firstLine.":".$line." linenums error-scroll'>";
					}

					for ($i = $firstLine; $i <= $lastLine; ++$i) {

						if ($this->_showFileFragment) {
							if ($i == $firstLine) {
								if (preg_match('#\*\/$#', rtrim($lines[$i-1]))) {
									$lines[$i-1] = str_replace("* /", "  ", $lines[$i-1]);
								}
							}
						}

						if ($lines[$i-1] != PHP_EOL) {
							$lines[$i-1] = str_replace("\t", "  ", $lines[$i-1]);
							echo htmlentities($lines[$i-1], ENT_COMPAT, 'UTF-8');
						} else {
							echo '&nbsp;'."\n";
						}
					}
					echo '</pre>';

					echo '<table cellspacing="0">';
				}
			}
		}
		echo '</table></div>

		<div class="version">
			Phalcon Framework '.Version::get().'
		</div>

		';

		echo $this->getJsSources().'</body></html>';

		self::$_showActive = false;

		return false;
	}

}

/**
 * Sets the exception handler
 */
set_exception_handler(function($e){
	$b = new BeutyExceptions();
	return $b->handle($e);
});

/**
 * Sets the error handler
 */
set_error_handler(function($errorCode, $errorMessage, $errorFile, $errorLine)
{
	if (!(error_reporting() & $errorCode)) {
       	return;
    }

	$b = new BeutyExceptions();
	return $b->handleError($errorCode, $errorMessage, $errorFile, $errorLine);
});
