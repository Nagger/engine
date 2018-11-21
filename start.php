<?php
namespace rib;
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);

// Будем использовать следующие пространства имён
// DB, Utils, CRM, ibank

require "autoload.php";

// Типы обрабатываемых заявок
$typeRequest = array('card','close_depo','reissue','import_retail');
//$typeRequest = array('card','close_depo','reissue');

// Обрабатываем заявки

foreach ($typeRequest as $value) {
	$request = crm\Process::getInstance($value);
	$request->processConstrDoc();	
}





