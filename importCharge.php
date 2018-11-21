<?php
namespace rib;

error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);

require "autoload.php";

// ID Client
$client_id = 33770;
$from_date = date("Y-m-d", strtotime("-5 day"));
//$from_date = '2010-10-18';

// Получаем платежки

$listCharge = new ibentity\ListCharge($client_id, $from_date);
$list = $listCharge->import();

