# Pretty Exceptions

[Phalcon](http://phalconphp.com) is a web framework delivered as a C extension providing high
performance and lower resource consumption.

Pretty Exceptions is an utility to show exceptions/errors/warnings/notices using a nicely visualization.

This utility is not intended to be used in production.

The code in this repository is written in PHP.

## Automatic Usage

The easiest way to use this utility is include its 'loader':

```php
require '/path/to/pretty-exceptions/loader.php';
```

## Manual include

Or you could include the utility manually or via an autoloader:

```php

//Requiring the file
require '/path/to/pretty-exceptions/Library.php';

//Or using an autoloader
$loader = new Phalcon\Loader();

$loader->registerNamespaces(array(
        'Phalcon\\Utils' => '/path/to/pretty-exceptions/Library/Phalcon/Utils/'
));

$loader->register();

```

## Usage

Listen for exceptions:

```php

set_exception_handler(function($e)
{
	$p = new \Phalcon\Utils\PrettyExceptions();
	return $p->handle($e);
});

```

Listen for user errors/warnings/notices:

```php

set_error_handler(function($errorCode, $errorMessage, $errorFile, $errorLine)
{
	if (!(error_reporting() & $errorCode)) {
       	return;
    }

	$p = new \Phalcon\Utils\PrettyExceptions();
	return $p->handleError($errorCode, $errorMessage, $errorFile, $errorLine);
});

```

## Options

The following is the way to configure the utility:

```php

$p = new \Phalcon\Utils\PrettyExceptions();

//Change the base uri for static resources
$p->setBaseUri('/');

//Set whether if open the user files and show its code
$p->showFiles(true);

//Set whether show the complete file or just the relevant fragment
$p->showFileFragment(true);

//Change the CSS theme (default or night)
$p->setTheme('default');

```

## Live Demo

A live demo is available [here](http://test.phalconphp.com/exception.html)
