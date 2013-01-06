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

namespace Phalcon\Utils;

require __DIR__.'/Library/Phalcon/Utils/PrettyExceptions.php';

/**
 * Sets the exception handler
 */
set_exception_handler(function($e)
{
	$p = new PrettyExceptions();
	return $p->handle($e);
});

/**
 * Sets the error handler
 */
set_error_handler(function($errorCode, $errorMessage, $errorFile, $errorLine)
{
	if (!(error_reporting() & $errorCode)) {
		return;
	}

	$p = new PrettyExceptions();
	return $p->handleError($errorCode, $errorMessage, $errorFile, $errorLine);
});
