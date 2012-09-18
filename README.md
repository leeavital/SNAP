SNAP
====

What Is It?
-----------

SNAP stands for __s__hared __n__amespace __a__pplication __p__rogramming.

It allows you to write a function in PHP and then allow it to be called in javascript.


How To Use It
-------------
First write some php:

```php
// in bar.php
function bar(){
	return something-on-the-server;
}
```


Then create the module.
```php
// in foo.js.php

// include the library.
require_once "dir/to/snap.php";


// create a new module.
$module = new SNAPModule("SNAP");


// register the function bar() in bar.php as foo.
$module->registerFunction("foo", "bar", "bar.php");


// write JS to the page.
SNAPTrigger($module);

```

The file name <code>foo.js.php</code> has it's extension because it will be interpreted as javascript (it's headers will be set to javascript)

The first line is a standard include.

The second line creates a new SNAPModule which can contain any number of functions. The constructors parameter is the name of the module--SNAPTrigger is called, a javascript object by that name ("SNAP") will be created.

The next line registers the function to the module. 
* the first parameter is the name the function will have in javascript.
* the second parameter is the name of the function of itself.
* the third parameter is the name of the file where the function can be found.








