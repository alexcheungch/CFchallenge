<?php
date_default_timezone_set("Asia/Hong_Kong");
require_once('TradeMessageClass.php');

// const LOGFILE = "trademessage.log";
$trade_parameters = array(
	'storage' => 'file',
    'filename'=> 'trademessage-sample.log'
);

$processor = new TradeMessageProcessor($trade_parameters);

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

$allapi = array();
$reporter = new TradeMessageFrontend($processor);
$allapi['reportByCountry'] =$reporter->reportByCountry();
$reporter = new TradeMessageFrontend($processor);
$allapi['reportByCurrencyFrom'] =$reporter->reportByCurrencyFrom();
$reporter = new TradeMessageFrontend($processor);
$allapi['reportByCurrencyTo'] =$reporter->reportByCurrencyTo();
$reporter = new TradeMessageFrontend($processor);
$allapi['getList'] =$reporter->getList();

echo json_encode($allapi);
?>