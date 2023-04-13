<?php
// Read the variables sent via POST from our API
$sessionId   = $_POST["sessionId"];
$serviceCode = $_POST["serviceCode"];
$phoneNumber = $_POST["phonenumber"];
//$moneyboxaccountnumber = $_POST["moneyboxaccount"];
$fname = $_POST["fname"];
//$accountNumber = $_POST["AccountNumber"];
$text        = $_POST["text"];

include('dbconnect.php');

$sqlQuery = "SELECT `fname`, `lname`, `moneyboxaccount`, `familyid`, `phonenumber`, `pin`, `amount` FROM `trustee_info` WHERE phonenumber='".$phoneNumber."'";
            $resultSet = mysqli_query($conn, $sqlQuery);
            $isValidLogin = mysqli_num_rows($resultSet);
                 $userDetails = mysqli_fetch_assoc($resultSet);
                 $fname = $userDetails['fname'];
                 $pin = $userDetails['pin'];

if ($text == "") {
    // This is the first request. Note how we start the response with CON
    $response  = " CON Welcome to Lifetime Moneybox. What would you like to do? \n";
    
    $response .= "1. Account \n";
    $response .= "2. Auto Debit \n";
    $response .= "3. Make Contribution \n";
    $response .= "4. Call back \n";

} else if ($text == "1") {
    // Business logic for first level response
    $response = "CON Choose account information you want to view \n";
    $response .= "1. Family ID \n";

} else if($text == "1*1") { 
    // This is a second level response where the user selected 1 in the first instance
    $moneyboxaccountnumber  = "SELECT moneyboxaccount FROM `trustee_info`";
    $phonenumber = "SELECT phonenumber FROM `trustee_info`";

} else if ($text == "2") {
    // Business logic for first level response
    $response = "CON Choose account information you want to view \n";
    $response .= "1. Enter pin \n";

} else if ($text == "2*1") {
    // Business logic for first level response
    // This is a terminal request. Note how we start the response with END
    $id= 'OFCOC0001';
    $sql= 'SELECT * FROM trustee_info WHERE familyid = ?';
    $start=$conn ->prepare($sql);
    $start ->execute([$sql]);
    while ($res = $start ->fetch()) {
        $response = "END <br><u>Trustee details:";
    }

} else if ($text == "3") {
    // Business logic for first level response
    $response = "CON Choose payment method \n";
    $response .= "1. Mobile Money \n";
    $response .= "2. Debit Card \n";

} else if($text == "3*1") { 
    // This is a second level response where the user make payment
    $response = "1. MTN \n";
    $response .= "2. Vodafone \n";
    $response .= "3. TIGO \n";

//} else if ($text == "3*1*1") {
    // This will show when the user chooses mtn
    //$response = "1. How much do you want to contribute \n";
    //$response .= "2. How frquently do you want to contribution \n";
} 
else if ($text==3*1*1){
    //mtn coding for payment(request to pay)
    // This sample uses the Apache HTTP client from HTTP Components (http://hc.apache.org/httpcomponents-client-ga/)
require_once 'HTTP/Request2.php';

$request = new Http_Request2('https://sandbox.momodeveloper.mtn.com/collection/v1_0/requesttopay');
$url = $request->getUrl();

$headers = array(
    // Request headers
    'Authorization' => '',
    'X-Callback-Url' => 'https://webhook.site/bd83d022-af31-43b1-ba12-b60850810c02',
    'X-Reference-Id' => '1ebe2921-9aea-4f79-8913-b4d91b85685d',
    'X-Target-Environment' => 'sandbox',
    'Content-Type' => 'application/json',
    'Ocp-Apim-Subscription-Key' => '{fd30c11b4fbe4169a3c06f1aa3b89652}',
);

$request->setHeader($headers);

$parameters = array(
    // Request parameters
);

$url->setQueryVariables($parameters);

$request->setMethod(HTTP_Request2::METHOD_POST);

// Request body
$request->setBody("{"amount": "string",
    "currency": "string",
    "externalId": "string",
    "payer": {
      "partyIdType": "MSISDN",
      "partyId": "0248900839"
    },
    "payerMessage": "Contribute towards your lifetime insurance",
    "payeeNote": "thanks for contributing"}");
}

try
{
    $response = $request->send();
    echo $response->getBody();
}
catch (HttpException $ex)
{
    echo $ex;
}
    

}



    $response = "END Your phone number is ".$phoneNumber;
    $response = "END Your account number is ".$moneyboxaccount;


    // This is a terminal request. Note how we start the response with END
    $response = "END Your account number is ".$moneyboxaccount;



// Echo the response back to the API
header('Content-type: text/plain');
echo $response
?>