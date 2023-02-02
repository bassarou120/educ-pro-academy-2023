<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* CodeIgniter
*
* An open source application development framework for PHP 5.1.6 or newer
*
* @package		CodeIgniter
* @author		ExpressionEngine Dev Team
* @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
* @license		http://codeigniter.com/user_guide/license.html
* @link		http://codeigniter.com
* @since		Version 1.0
* @filesource
*/

/* generate token*/
if (! function_exists('gen_token')) {
    function gen_token ($MTN_SUBSCRIPTION_KEY = '',$MTN_USE_ID='', $MTN_API_KEY='') {
        try
        {
            $headers1 = array('Accept' => 'application/json', "Ocp-Apim-Subscription-Key"=>$MTN_SUBSCRIPTION_KEY, );
            $options1 = array('auth' => array($MTN_USE_ID, $MTN_API_KEY));
//            $response= Requests::post("https://sandbox.momodeveloper.mtn.com/collection/token/",$headers1,array(),  $options1);
//            $response= Requests::post("https://ericssondeveloperapi.azure-api.net/collection/token/",$headers1,array(),  $options1);
            $response= Requests::post("https://proxy.momoapi.mtn.com/collection/token/",$headers1,array(),  $options1);


            $token = json_decode($response->body)->access_token;

//            var_dump($token);
//            die();

            return $token;


        }catch (HttpException $ex1){
            return null;

        }



    }
}


/* send Request to pay*/
if (! function_exists('send_request_to_pay')) {
    function send_request_to_pay ($token = '',$MTN_SUBSCRIPTION_KEY='', $amount='',$phone='' ,$X_Target="sandbox") {
        try
        {
            $X_Reference_Id = guidv4();
            $headers2 = array(
                "Authorization" => "Bearer ".$token,
                "X-Reference-Id"=> $X_Reference_Id ,
                "X-Target-Environment" => $X_Target,
                "Content-Type" => "application/json",
                "Ocp-Apim-Subscription-Key"=>$MTN_SUBSCRIPTION_KEY,

            );
//            "amount"=>$this->input->post('amount'),
            $data = json_encode([
                "amount"=>$amount,
                "currency"=> "XOF",
                "externalId"=> "112",
                "payer"=>[
                    "partyIdType"=> "MSISDN",
                    "partyId"=>$phone,
                ],
                "payerMessage"=>  "Payement de cours sur Educ-pro academy",
                "payeeNote"=>  "payer note",

            ]);
//            $response2=Requests::post("https://sandbox.momodeveloper.mtn.com/collection/v1_0/requesttopay", $headers2,$data, []);
//            $response2=Requests::post("https://ericssondeveloperapi.azure-api.net/collection/v1_0/requesttopay", $headers2,$data, []);
            $response2=Requests::post("https://proxy.momoapi.mtn.com/collection/v1_0/requesttopay", $headers2,$data, []);

            $data=['response'=>$response2,'X_Reference_Id'=>$X_Reference_Id];


            return $data ;


        }catch (HttpException $ex1){
            return null;

        }



    }
}



/* get Request to pay status*/
if (! function_exists('get_request_to_pay_status')) {
    function get_request_to_pay_status ($token = '',$MTN_SUBSCRIPTION_KEY='', $X_Reference_Id='',$X_Target="sandbox" ) {
        try
        {
            $headers3 = array('Accept' => 'application/json',
                "Ocp-Apim-Subscription-Key"=>$MTN_SUBSCRIPTION_KEY,
                'X-Target-Environment' => $X_Target,
                'Authorization' => 'Bearer '.$token,
            );

//            $response3= Requests::get('https://sandbox.momodeveloper.mtn.com/collection/v1_0/requesttopay/'.$X_Reference_Id, $headers3  );
//            $response3= Requests::get('https://ericssondeveloperapi.azure-api.net/collection/v1_0/requesttopay/'.$X_Reference_Id, $headers3  );
            $response3= Requests::get('https://proxy.momoapi.mtn.com/collection/v1_0/requesttopay/'.$X_Reference_Id, $headers3  );

            $dataResponse= json_decode($response3->body);
            return $dataResponse;


        }catch (HttpException $ex1){
            return null;

        }



    }
}




/* generate UID version 4 */
if (! function_exists('guidv4')) {
    function guidv4($data = null) {
            // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
            $data = $data ?? random_bytes(16);
            assert(strlen($data) == 16);

            // Set version to 0100
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
            // Set bits 6-7 to 10
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

            // Output the 36 character UUID.
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }



}






// ------------------------------------------------------------------------
/* End of file addon_helper.php */
/* Location: ./system/helpers/common.php */
