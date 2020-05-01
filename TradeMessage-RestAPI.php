<?php
date_default_timezone_set("Asia/Hong_Kong");
require_once('TradeMessageClass.php');

// setup the trademessage processor parameters
$trade_parameters = array(
    'id_length' => 6,
    'min_amount'=> 0,
    'currency_list' => "EUR GBP USD HKD CNY JPY",
    'country_list'  => "FR UK US HK CN JP"
);


$sample_ = array(
"userId"=>"134256", "currencyFrom"=>"EUR","currencyTo"=>"GBP",
"amountSell"=>1000,"amountBuy"=>747.10,"rate"=>0.7471,
"timePlaced"=>"29-Apr-2020 16:00:00","originatingCountry"=>"FR"
);
$sample_json=json_encode($sample_);

$trade = "";
if (isset($_GET['trade'])) {
  $trade = $_GET['trade'];
} elseif (isset($_POST['trade'])) {
  $trade = $_POST['trade'];
};

if ($trade == ""){
  // output sample json if nothing input  
  $trade = $sample_json;
};


$processor = new TradeMessageProcessor($trade_parameters);

$thistrade = json_decode($trade);
$trademessage = new TradeMessageEndpoint($processor);
$trademessage->load($thistrade);
$result = $trademessage->process();

// return as HTTP response in JSON format
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
echo json_encode($trademessage); // for php v5.4 or above   

?>
