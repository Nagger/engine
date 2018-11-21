<?php

spl_autoload_extensions(".php");
spl_autoload_register('myNamespaceAutoload');

function myNamespaceAutoload ($path) {
	if  (preg_match('/\\\\/ ', $path)) {
		$path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
	}
	if (file_exists("{$path}.php")) {
		require_once("{$path}.php");
	}
}
