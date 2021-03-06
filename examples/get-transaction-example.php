<?php
 /*********************************************************************************
 *  This transaction endpoint helps to retreive a particular transaction record by
 * means of the record ID..
***********************************************************************************/

require('../src/PaymexxClient.php');

// setting request header for authorization with Rest API
$header = array('username'=>PaymexxClient::$secret_key,'password'=>'');
PaymexxClient::headers($header);
// where $endpoint = endpoint method for API call, $method is either GET,POST,PUT or DELETE,
//  $params = array with data payload to form request body, $format = json or xml
$params = array('id'=>20);
$format = 'json';
$result = PaymexxClient::restClient('transaction', 'get', $params, $format);

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


