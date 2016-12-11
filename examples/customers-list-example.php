<?php
 /**********************************************************************************
 *  List customers endpoint gives the ability to retreive all active customers that 
 * have made payments to a merchant via Paymexx gateway.
**********************************************************************************/

require('../src/PaymexxClient.php');

// setting request header for authorization with Rest API
$header = array('username'=>PaymexxClient::$secret_key,'password'=>'');
PaymexxClient::headers($header);
// where $endpoint = endpoint method for API call, $method is either GET,POST,PUT or DELETE,
//  $params = array with data payload to form request body, $format = json or xml
$params = array('env'=>'test');
$format = 'json';
$result = PaymexxClient::restClient('customers', 'get', $params, $format);

if($format == 'json'){
  $res = json_decode($result,TRUE); 
   echo '<pre>';
  echo json_encode($res,JSON_PRETTY_PRINT);
  echo '</pre>';
}else if($format =='xml'){
     $xml = new SimpleXMLElement($result);
     $output = (string) $xml->asXML(); 
    echo htmlentities($output,ENT_XML1,'utf-8',true);
    
}


