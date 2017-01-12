<?php
/*--------------------------------------------- 
 * Document: Paymexx PHP Client LIbrary
 * Author: James Abiagam
 * Created on: 10-12-2016 19:23
 * Description: The PHP Client library for Paymexx REST API
 * Version: 1.0
 *--------------------------------------------*/
class PaymexxClient{
    
    
    private static $instance;
 
    /************************************************
    * A base class for setting commonly needed values for
    * paymexx api integration
    * @var type 
    ************************************************/
    
    //@var string represents the development environment as either staging or production
     // test or live
    public static $environment = "test";
     
    //@var string The merchant ID from Paymexx Dashboard to be used for requests
     public static $merchant_key = "";
     
     //@var string The api secret key to be used for encryption
     public static $secret_key= "";
     
     public static $http_url_xml = "https://api.paymexx.com/";
     public static $http_url_json = "https://paymexx.com/";
     public static $base_url;
     
     public $options = array();
     public $handle; // cURL resource handle.
    
    // Populated after execution:
    public $response; // Response body.
    public static $headers; // Parsed reponse header object.
    public $error; // Response error string.
    
     public  function __construct() {
        parent::__construct();
        self::setMerchantCredentials();
    }
    
    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
  * construct the Paymexx client
  * @param string $merchantKey
  * @param string $apiKey
  */
  public static function setMerchantCredentials(){
    if (empty(self::$merchant_key)) {
         throw new Exception ("Merchant key can not be empty");
    }
    if (empty(self::$secret_key)) {
         throw new Exception ("Api key can not be empty");
    }
    if (empty(self::$environment) || (self::$environment !== "test" && self::$environment !== "live")) {
         throw new Exception ("Environment variable can only be `test` or `live`");
    }
    
  }
  
  /**
   * Set array elements
   * @param type $key
   * @param type $value
   */
   public function set_option($key, $value){
        $this->options[$key] = $value;
   }
   
   /**
    * Set Request headers
    * @param array $_headers
    */
   public static function headers(array $_headers){
          self::$headers = $_headers;
   }
   
   /**
    * Sub-function for sending API calls
    * @param type $host
    * @param type $method
    * @param type $headers
    * @param type $parameters
    * @return type
    */
   public static function sendHttpRequest($host,$method,$headers,$parameters) {
          $params = '';
          $process = curl_init();
        if(($method == 'get') || ($method == 'delete')){
                   if(array_key_exists('id',$parameters)){
                           unset($parameters['env']);
                          $params = $parameters;
                }else{
                    $params = $parameters;
                }
            $qry = self::build_query($params);
            $_host = $host.'?'.$qry;
            curl_setopt($process, CURLOPT_URL, $_host);
            (($method == 'get')?curl_setopt($process, CURLOPT_HTTPGET, 1):'');
        }else if(($method == 'post') || ($method == 'put')){
           curl_setopt($process, CURLOPT_URL, $host);
        }
        curl_setopt($process, CURLOPT_TIMEOUT, 720);
        curl_setopt($process, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($process, CURLOPT_SSL_VERIFYHOST, 0);
        $username = ((array_key_exists('username',$headers)? $headers['username']:''));
        $_password = ((array_key_exists('password',$headers)? $headers['password']:''));
        $password = ((empty($_password))?$_password:'');
        curl_setopt($process, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        if($method == 'post'){
          // curl_setopt($process, CURLOPT_HEADER, 1);
           curl_setopt($process, CURLOPT_POST, 1);
           curl_setopt($process, CURLOPT_POSTFIELDS, self::set_fields($parameters));
        }else if($method == 'put'){
            //curl_setopt($process, CURLOPT_PUT, true);
            curl_setopt($process, CURLOPT_HEADER, 0);
            curl_setopt($process, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($process, CURLOPT_POSTFIELDS, self::set_fields($parameters));
         }else if($method == 'delete'){
           curl_setopt($process, CURLOPT_HEADER, 0);
           curl_setopt($process, CURLOPT_CUSTOMREQUEST, 'DELETE'); 
        }
        $return = curl_exec($process);
        curl_close($process);
        return $return;
    }
    
    /**
     * Build request payload 
     * @param type $params
     * @return string
     */
    public static function build_query($params){
        $query = '';
        if(!empty($params)){
            foreach($params as $key=>$val){
               $query .= $key.'='.$val.'&'; 
            }
        }
        return rtrim($query,'&');
    }
    
    /**
     * url-ify the data for the POST
     * @param type $fields
     * @return string
     */
    public static function set_fields($fields) {
        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');
        return $fields_string;
    }

     /**
     * Prepare the REST URL based on the environment
     * variable and response format
     * @param type $format
     */
    public static function setRequest($format){
         switch(self::$environment){
          case 'test':
                    if($format == 'json'){
                       self::$base_url = self::$http_url_json.'sandbox/api/'; 
                    }else if($format == 'xml'){
                       self::$base_url = self::$http_url_xml.'sandbox/beta/'; 
                    }
              break;
           case 'live':
                    if($format == 'json'){
                       self::$base_url = self::$http_url_json.'v1/api/'; 
                    }else if($format == 'xml'){
                       self::$base_url = self::$http_url_xml.'1.0/beta/'; 
                    }
              break;
         }
    }
    
    /**
     * Front facing function to send API request
     * @param type $endpoint
     * @param type $method
     * @param type $params
     * @param type $format
     * @return type
     */
    public static function restClient($endpoint,$method,$params,$format){
          self::setRequest($format); // Set Curl url based on environment
          $host = self::$base_url.$endpoint;
          $env_array = array('env'=> self::$environment);
          $_params = array_merge($params,$env_array);
          $res = self::sendHttpRequest($host,strtolower($method),self::$headers,$_params);
          return $res;
    }
    
  /**
   * Utility function
   * @param type $array
   */ 
  public static function debug($array){
          echo '<pre>';
          echo  print_r($array);
          echo '</pre>';
              
  }



  
    
}



