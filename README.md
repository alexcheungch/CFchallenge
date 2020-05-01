# Challenge from CurrencyFair

# Introduction
As this challenge is a small and quick project. The development is based on PHP which can be hosted in my established cloud development server for tracking crypto-currency trend. 

This challenge is completed with agile approach with 3 sprints :
1. End Point, wrapped as Restful API and can be tested interactively and a test page, test-endpoint.php for multiple testing page
2. Processor, which is basically storing up the message. For less dependency on back-end infra-structure such as database and stateful server, a single logfile is used for storage. And the Processor can be checked easily by checking the logfile
3. Front-End, wrapped as Restful API and can be tested interactively. Moreover a HTML page is built using the Apache eCharts.js to build the bar chart for the processed data. 

# Design
3 Objects are implemented and defined in the TradeMessageClass.php : 
TradeMessageEndpoint, TradeMessageProcessor, and TradeMessageFrontend

TradeMessageEndpoint and TradeMessageFrontend used dependency injection to base on the parameters of the TradeMessageProcessor

# TradeMessageProcessor 
The Processor to handle the Trade Message.
Operation parameters allowed included :
1. Minimum length of UserId string, default is 1
2. Minimum amount of each trade, default is 0.
3. Currency support, e.g. "EUR GBP USD HKD CNY JPY"
4. Origination Country allowed, e.g. "FR UK US HK CN JP"

The Processor currently only process the message by storing it up

And to simplify the backend, the Processor now only implement file based storage, i.e. all messages are stored into a log file in the filesystem. The Processor can update the corresponding storage handling logics if database or stateful storage will be used.

And the PHP process should have write permission for the logfile on the file system. Default log file is trademessage.log on the program 

# TradeMessageEndpoint
Get its dependency injection based on the Processor
This Endpoint will validate the consumed Trade Message first,and then call the processor

The Endpoint is wrapped as a Restful API that accept the trade message as json string in the parameters, trade either from GET or POST
e.g. trademessage-RestAPI.php?trade=<trademessage>
and the API will return the consumed trade message with the validation result
or return the sample json if no trade is submitted

# Testing page 
test-endpoint.php, a testing page with mulitple test cases to test the validation condition of the EndPoint

# Live End Point for Testing GET or POST -> 
http://138.197.209.229/trademessage/TradeMessage-RestAPI.php?trade=

# TradeMessageFrontend
Get its dependency injection based on the Processor
This Frontend will report the consumed trade message and support the following reporting
getList - List all the messages
reportByCountry - report the number of message by originatingCountry
reportByCurrencyFrom - report the sub-total amount by currencyFrom
reportByCurrencyTo - report the sub-total amount by currencyTo
validate the consumed Trade Message first,and then call the processor

The Frontend is wrapped as Restful API that report based on the api parameters either from GET or POST

# Live FrontEnd for getting all received message ->
http://138.197.209.229/trademessage/TradeReport-RestAPI.php?api=getList

# A HTML page based on apache echarts.js (with included echarts.min.js) for Pie chart presentation ->
http://138.197.209.229/trademessage/Message_Reporter.html

# Improvements 

The current file based storage will limit the overall performance due to the file IO. A memory cached permanent storage can help to enhance, such as mongoDB

The PHP codes are a quick implementation and have not been optimized for the security and performance.
