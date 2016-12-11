<?php

/* ***************************************************************
 * This endpoint is used to create a ne invoice category or type
 * under a given merchant account. The proposed invoice MUST be
 * linked to a product group already created on the Merchant dashboard.
 **********************************************************************/

require('../src/PaymexxClient.php');

// setting request header for authorization with Paymexx Rest API
$header = array('username'=>PaymexxClient::$secret_key,'password'=>'');
PaymexxClient::headers($header);
// where $endpoint = endpoint method for API call, $method is either GET,POST,PUT or DELETE,
//  $params = array with data payload to form request body, $format = json or xml
$params = array('env'=>'test','name'=> 'Sunglasses Sale Invoice','product'=>'Baby Storefront',
    'vat'=>0.25,'summary'=>'An invoice category for sunglasses customers');
$format = 'json';
$result = PaymexxClient::restClient('invoicetype', 'post', $params, $format);

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

