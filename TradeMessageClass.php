<?php
class TradeMessageEndpoint
{
    protected $ID_LENGTH =1;
    protected $MIN_AMOUNT=0;
    protected $CURRENCYLIST = "";
    protected $COUNTRYLIST  = "";

    protected $processor;

    public function __construct($processor)
    {
      $this->processor = $processor;
      $parameters = $this->processor->getTradeParameters();
      if (isset($parameters['min_amount'])) $this->MIN_AMOUNT = $parameters['min_amount'];
      if (isset($parameters['id_length'])) $this->ID_LENGTH = $parameters['id_length'];
      if (isset($parameters['currency_list'])) $this->CURRENCYLIST = $parameters['currency_list'];
      if (isset($parameters['country_list'])) $this->COUNTRYLIST = $parameters['country_list'];      
    }

    public function load($data)
    {
      foreach ($data as $key => $value) $this->{$key} = $value;
      $this->validated = "loaded";
    }

    public function process()
    {
      if ($this->validate()) $this->processor->process($this);
      return $this->validated;
    }

    public function validate()
    {
      $checking = ":";
      $validated = false;
      if (!($this->isValidUserID())) $checking.="** InValid ** userId not defined or Length less than ".$this->ID_LENGTH;

      if (!($this->isValidRate())) $checking.="** InValid ** Rate or Trade Amount < ".$this->MIN_AMOUNT;

      if (!($this->isvalidCurrencyFrom())) $checking.="** InValid ** currencyFrom not defined or not in list ".$this->CURRENCYLIST;

      if (!($this->isvalidCurrencyTo())) $checking.="** InValid ** currencyTo not defined or not in list ".$this->CURRENCYLIST;

      if (!($this->isValidOriginatingCountry())) $checking.="** InValid ** originatingCountry not defined or not in list ".$this->COUNTRYLIST;

      if (strlen($checking) == 1) { 
        // all checking passed
        $checking="";
        $validated = true;
      };
      
      $this->validated = date("Y-m-d h:i:s").$checking;

      return $validated;
    }


    private function isValidUserID(){
       if (isset($this->userId)) {
          if (strlen($this->userId) >= $this->ID_LENGTH) {
             return true;
          } else {
             return false;
          }
       } else {
           return false;
       }
    }

    private function isValidRate(){      
      if (isset($this->amountSell) && isset($this->amountBuy) && isset($this->rate)) {
         if (is_numeric($this->amountSell) && is_numeric($this->amountBuy) && is_numeric($this->rate)) {
            if (($this->amountSell> $this->MIN_AMOUNT) && ($this->amountBuy>$this->MIN_AMOUNT) && ($this->rate>0)) {
//                if ($this->amountSell * $this->rate == $this->amountBuy) {
                   return true;
//                } else {
//                   return false;
//                }
            } else {
                return false;
            }
          } else {
             return false;
          }
      } else {
        return false;
      }
    }  

    private function isValidCurrencyFrom(){
      if (strlen($this->CURRENCYLIST) == 0){
         // No checking on Currency
         return true;
      } else {
       if (isset($this->currencyFrom)) {
          if ((strlen($this->currencyFrom) == 3) && (strpos($this->CURRENCYLIST,$this->currencyFrom) !== false)) {
             return true;
          } else {
             return false;
          }
       } else {
           return false;
       }
      } 
    }

    private function isValidCurrencyTo(){       
      if (strlen($this->CURRENCYLIST) == 0){
         // No checking on Currency
         return true;
      } else {      
       if (isset($this->currencyTo)) {
          if ((strlen($this->currencyTo) == 3) && (strpos($this->CURRENCYLIST,$this->currencyTo) !== false)) {
             return true;
          } else {
             return false;
          }
       } else {
           return false;
       }
      }
    }

    private function isValidOriginatingCountry(){      
      if (strlen($this->COUNTRYLIST) == 0){
         // No checking on Country
         return true;
      } else {
       if (isset($this->originatingCountry)) {
          if ((strlen($this->originatingCountry) == 2) && (strpos($this->COUNTRYLIST,$this->originatingCountry) !== false)) {
             return true;
          } else {
             return false;
          }
       } else {
           return false;
       }
      } 
    }
};

class TradeMessageProcessor
{
  protected $parameters = array(
    'ID_LENGTH' => 1,
    'MIN_AMOUNT'=> 0,
    'CURRENCYLIST' => "",
    'COUNTRYLIST'  => ""
//    'CURRENCYLIST' => "EUR GBP USD HKD CNY JPY",
//    'COUNTRYLIST'  => "FR UK US HK CN JP"
  );

  protected $storage = 'file';
  protected $logfilename = "trademessage.log";
  private $logger;
  
  public function __construct(array $parameters = array())
  {
    if (isset($parameters['storage'])) $this->storage = $parameters['storage'];
    if ($this->storage == 'file') {
      if (isset($parameters['filename'])) $this->logfilename = $parameters['filename'];
    } else {
      // implement database or session if available    
      // default is still the file logger     
    };
    if (isset($parameters['id_length'])) $this->parameters['ID_LENGTH'] = $parameters['id_length'];
    if (isset($parameters['min_amount'])) $this->parameters['MIN_AMOUNT'] = $parameters['min_amount'];
    if (isset($parameters['currency_list'])) $this->parameters['CURRENCYLIST'] = $parameters['currency_list'];
    if (isset($parameters['country_list'])) $this->parameters['COUNTRYLIST'] = $parameters['country_list']; 
  }

  public function process($trademessage)
  {
      $this->connectStorage();
      $this->save($trademessage);
  }

  public function getTradeParameters()
  {
    $thisparameters = array();
    if (isset($this->parameters['ID_LENGTH'])) $thisparameters['id_length'] = $this->parameters['ID_LENGTH'];
    if (isset($this->parameters['MIN_AMOUNT'])) $thisparameters['min_amount'] = $this->parameters['MIN_AMOUNT'];
    if (isset($this->parameters['CURRENCYLIST'])) $thisparameters['currency_list'] = $this->parameters['CURRENCYLIST'];
    if (isset($this->parameters['COUNTRYLIST'])) $thisparameters['country_list'] = $this->parameters['COUNTRYLIST'];
    return $thisparameters;
  }

  public function getTradeMessageStorage()
  {
    $storage_parameters = array();
    $storage_parameters['storage'] = $this->storage;
    if ($this->storage == 'file') $storage_parameters['filename'] = $this->logfilename;
    return $storage_parameters;
  }
  
  protected function connectStorage()
  {
    if ($this->storage == 'file') {
      if ($this->logger === null) {
        $this->logger = fopen($this->logfilename,'a');
        if ($this->logger === false){
          throw new RuntimeException('cannot open log file');
        }
      }
      register_shutdown_function(array($this,'close'));
    } else {
      // connect to database or stateful storage
    }
  }

  protected function save($data)
  {
    if ($this->storage == 'file') {
      fwrite($this->logger,json_encode($data).PHP_EOL);
    } else {
      // save to database or stateful storage
    }
  }

  public function close() 
  {
    if ($this->logger !==null) {
      fclose($this->logger);
    }
  }

};

class TradeMessageFrontend
{
  protected $storage;
  protected $logfilename;
  protected $logger;
  
  public function __construct($processor)
  {
    $parameters = $processor->getTradeMessageStorage();
    if (isset($parameters['storage'])) $this->storage = $parameters['storage'];
    if ($this->storage == 'file') {
      if (isset($parameters['filename'])) $this->logfilename = $parameters['filename'];
    } else {
      // implement database or session if available    
      // default is still the file logger     
    }
  }

  public function getList()
  {
      $this->connectStorage();
      $alltrades = array();
      while (($thisrecord = $this->getRecord()) !== false) {
          $thistrade = json_decode($thisrecord);
          $thisarr = (array) $thistrade;
          $alltrades[]= $thisarr;
      }
      return json_encode($alltrades);
  }

  public function reportByCountry()
  {
      $this->connectStorage();
      $byCountry = array();
      while (($thisrecord = $this->getRecord()) !== false) {
          $thistrade = json_decode($thisrecord);
          $thisarr = (array) $thistrade;
          if (isset($thisarr['originatingCountry'])) 
            if (isset($byCountry[$thisarr['originatingCountry']])) {
              $byCountry[$thisarr['originatingCountry']]++;
            } else {
              $byCountry[$thisarr['originatingCountry']] = 1;
            }
      }
      return json_encode($byCountry);
  }

  public function reportByCurrencyFrom()
  {
      $this->connectStorage();
      $byCurrency = array();
      while (($thisrecord = $this->getRecord()) !== false) {
          $thistrade = json_decode($thisrecord);
          $thisarr = (array) $thistrade;
          if ((isset($thisarr['currencyFrom'])) && isset($thisarr['amountSell']))
            if (isset($byCurrency[$thisarr['currencyFrom']])) {
              $byCurrency[$thisarr['currencyFrom']] += $thisarr['amountSell'];
            } else {
              $byCurrency[$thisarr['currencyFrom']] = $thisarr['amountSell'];
            }
      }
      return json_encode($byCurrency);
  }

  public function reportByCurrencyTo()
  {
      $this->connectStorage();
      $byCurrency = array();
      while (($thisrecord = $this->getRecord()) !== false) {
          $thistrade = json_decode($thisrecord);
          $thisarr = (array) $thistrade;
          if ((isset($thisarr['currencyTo'])) && isset($thisarr['amountBuy']))
            if (isset($byCurrency[$thisarr['currencyTo']])) {
              $byCurrency[$thisarr['currencyTo']] += $thisarr['amountBuy'];
            } else {
              $byCurrency[$thisarr['currencyTo']] = $thisarr['amountBuy'];
            }
      }
      return json_encode($byCurrency);
  }

  protected function connectStorage()
  {
    if ($this->storage == 'file') {
      if ($this->logger === null) {
        $this->logger = fopen($this->logfilename,'r');
        if ($this->logger === false){
          throw new RuntimeException('cannot open log file');
        }
      }
      register_shutdown_function(array($this,'close'));
    } else {
      // connect to database or stateful storage
    }
  }

  protected function getRecord()
  {
    if ($this->storage = 'file') {
      return fgets($this->logger);
    } else {
      // return from database or stateful storage
    }
  }

  public function close() 
  {
    if ($this->logger !==null) {
      fclose($this->logger);
    }
  }
};
?>