<?php
namespace rib;

error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);

require "autoload.php";

// ID Client
// Возможность выбора по клиенту и дате оставил на всякий случай
$client_id = 33770;
$from_date = date("Y-m-d", strtotime("-5 day"));
//$from_date = '2018-09-05';

// Получаем платежки из очереди

$listCharge = new ibentity\ListCharge($client_id, $from_date);
$list = $listCharge->process();

