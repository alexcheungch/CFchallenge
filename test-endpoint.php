<?php
date_default_timezone_set("Asia/Hong_Kong");
require_once('TradeMessageClass.php');

// setup the trademessage processor parameters
$trade_parameters = array(
    'id_length' => 6,
    'min_amount'=> 0,
    'currency_list' => "EUR GBP USD HKD CNY JPY",
    'country_list'  => "FR UK US HK CN JP",
    'storage' => 'file',
    'filename' => '/tmp/testing.log'
);

// Test data
$test = array();
$nowtime= new DateTime('-1 hour');
$testtime= $nowtime->format("d-M-Y h:i:s");

$test[] = array(
"userId"=>"134256", "currencyFrom"=>"EUR","currencyTo"=>"GBP",
"amountSell"=>1000,"amountBuy"=>747.10,"rate"=>0.7471,
"timePlaced"=>$testtime,"originatingCountry"=>"FR"
);

$test[] = array(
"userId"=>"1234567", "currencyFrom"=>"EUR","currencyTo"=>"GBP",
"amountSell"=>1000,"amountBuy"=>747.10,"rate"=>0.7471,
"timePlaced"=>$testtime,"originatingCountry"=>"FR"
);

$test[] = array(
"userId"=>"123456", "currencyFrom"=>"ABC","currencyTo"=>"GBP",
"amountSell"=>1000,"amountBuy"=>747.10,"rate"=>0.7471,
"timePlaced"=>$testtime,"originatingCountry"=>"FR"
);

$test[] = array(
"userId"=>"123456", "currencyFrom"=>"EUR","currencyTo"=>"DEF",
"amountSell"=>1000,"amountBuy"=>747.10,"rate"=>0.7471,
"timePlaced"=>$testtime,"originatingCountry"=>"FR"
);

$test[] = array(
"userId"=>"123456", "currencyFrom"=>"EUR","currencyTo"=>"GBP",
"amountSell"=>1000,"amountBuy"=>747.10,"rate"=>0.7471,
"timePlaced"=>$testtime,"originatingCountry"=>"XY"
);

$test[] = array(
"userId"=>"123456", "currencyFrom"=>"EUR","currencyTo"=>"GBP",
"amountSell"=>1000,"amountBuy"=>747.10,"rate"=>0.7472,
"timePlaced"=>$testtime,"originatingCountry"=>"FR"
);

$test[] = array(
"userId"=>"1234567", "currencyFrom"=>"ABC","currencyTo"=>"ABC",
"amountSell"=>1000,"amountBuy"=>747.10,"rate"=>0.7472,
"timePlaced"=>$testtime,"originatingCountry"=>"XY"
);

$processor = new TradeMessageProcessor($trade_parameters);
$trademessage = new TradeMessageEndpoint($processor);

$results = array();

foreach ($test as $test_) {
  $test_json = json_encode($test_);	
  $thistrade = json_decode($test_json);
  $trademessage->load($thistrade);
  $result = $trademessage->process();
  $test_['validation'] = $result;
  $results[] = $test_;
//  $results[]= (array) $trademessage;
}

// return as HTTP response in JSON format
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
echo json_encode($results); // for php v5.4 or above

?>