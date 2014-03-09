<?php 

require_once 'bootstrap.php'; // Must be the first of all inclusions 
require_once 'api_functions.php';


// Never change this 
$initialization_vecto = '@#^!$&%$^%^#%@!&$';
$api_keys = array('key'=>'0011100101001');


function _api_call($pri_key,$api_callback_name,$args){
	$callback_name = "api_$api_callback_name";
	if(function_exists($callback_name)){
		$ret_val = $callback_name($args);
	}else{
		$ret_val = _api_error("Unknown callback '$api_callback_name'");
	}
	if (!$ret_val) {
		$ret_val = _api_error("callback returned nothing");		
	}
	$r = _api_encrypt($ret_val,$pri_key);
	if (!$r) {
		$r = "api callback failed because of an unknown error";
	}
	return $r;
}

function _api_error($reason,$status = -1,$die = false){
	$v = array("status"=>$status,"reason"=>$reason);
	if($die == true){
		die(json_encode($v));
	}else{
		return json_encode($v);
	}
}


function get_private_key($public_key){
	global $api_keys;
	if(isset($public_key)){
		if(isset($api_keys[$public_key])){
			return $api_keys[$public_key];
		}		
	}
	return null;
}


function _api_encrypt($string,$secret_key){
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_iv = ''; // Never change this value!!
    // hash
    $key = hash('sha256', $secret_key);
    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
    $output = base64_encode($output);
    return $output;
}


function _api_decrypt($string,$secret_key) {
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_iv = ''; // Never change this value!!
    // hash
    $key = hash('sha256', $secret_key);
    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);    
    return $output;
}
