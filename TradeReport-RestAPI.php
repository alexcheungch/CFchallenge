<?php
date_default_timezone_set("Asia/Hong_Kong");
require_once('TradeMessageClass.php');

$trade_parameters = array(
	'storage' => 'file',
    'filename'=> 'trademessage.log'
);

$api = "";
// Get Input from URL GET /POST
if (isset($_GET['api'])) {
  $api = $_GET['api'];
} elseif (isset($_POST['api'])) {
  $api = $_POST['api'];
};

if ($api == ""){
  echo 'Not supported';
} else {
  $processor = new TradeMessageProcessor($trade_parameters);

  $reporter = new TradeMessageFrontend($processor);

  // return as HTTP response in JSON format
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json; charset=UTF-8');

  if ($api == 'reportByCurrencyTo' ) echo $reporter->reportByCurrencyTo();
  if ($api == 'reportByCurrencyFrom') echo $reporter->reportByCurrencyFrom();
  if ($api == 'reportByCountry') echo $reporter->reportByCountry();
  if ($api == 'getList') echo $reporter->getList(); 
};

?>