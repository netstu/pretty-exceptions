<?php

//namespace Phalcon;

class BeutyExceptions
{

	public function printCssSources()
	{
		echo '<link href="/beauty-exceptions/beauty.css" type="text/css" rel="stylesheet" />
		<script type="text/javascript" src="/beauty-exceptions/prettify/prettify.js"></script>';
	}

	public function handle($e)
	{
		//$time = microtime(true);

		echo $this->printCssSources();

		echo '<html><head></head><body>';

		echo '<div class="error-main">', get_class($e), ': ', $e->getMessage(), '</div>';

		echo '<div class="error-backtrace"><table cellspacing="0">';
		foreach ($e->getTrace() as $n => $trace) {

			echo '<tr><td align="right" valign="top" class="error-number">#', $n, '</td><td>';
			if (isset($trace['class'])) {
				echo '<span class="error-class">', $trace['class'], '</span>', $trace['type'];
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

			if (isset($trace['file'])) {
				echo '</table>';

				$line = $trace['line'];
				$lines = file($trace['file']);

				$numberLines = count($lines);
				$firstLine = ($line - 7) < 1 ? 1 : $line - 7;
				$lastLine = ($line + 5 > $numberLines ? $numberLines : $line + 5);

				/*echo "<pre class='prettyprint lang-php linenums:".$firstLine."'>";
				for ($i = $firstLine; $i <= $lastLine; ++$i) {

					if ($i == $firstLine) {
						if (preg_match('#\*\/$#', rtrim($lines[$i-1]))) {
							$lines[$i-1] = str_replace("* /", "  ", $lines[$i-1]);
						}
					}

					if ($lines[$i-1] != PHP_EOL) {
						$lines[$i-1] = str_replace("\t", "  ", $lines[$i-1]);
						echo htmlentities($lines[$i-1], ENT_COMPAT, 'UTF-8');
					} else {
						echo '&nbsp;'."\n";
					}
				}
				echo '</pre>';*/

				echo '<table cellspacing="0">';
			}
		}
		echo '</table></div>';

		echo '<script type="text/javascript">prettyPrint();</script>';

		echo '</body></html>';
	}
}

set_exception_handler(function($e){
	$b = new BeutyExceptions();
	$b->handle($e);
});

//throw new Exception('Oh god why?');