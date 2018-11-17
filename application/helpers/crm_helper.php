<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
header('Content-type: text/html; charset=UTF-8');

#defined('URI_CRM') OR define('URI_CRM', 'http://labella.quanlyspa.com/api/appointment');
defined('URI_CRM') OR define('URI_CRM', 'http://192.168.1.253:8082/api/appointment');

defined('CALL_OB') OR define('CALL_OB', 'http://192.168.1.17/ob.php');
defined('ACOUNT_RF') OR define('ACOUNT_RF', 'http://192.168.1.17/rs.php');

if( ! function_exists('fnc_acount_rf')){
	function fnc_acount_rf(){
		$url_rf = ACOUNT_RF;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL			, $url_rf);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER	, TRUE);
		$result = curl_exec($ch);
	    curl_close($ch);
	}
}

if( ! function_exists('fnc_call_ob')){
	function fnc_call_ob($ext, $phone){
		$url_call = CALL_OB . '?txtextension=' . $ext . '&txtphonenumber=' . $phone;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL			, $url_call);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER	, TRUE);
		$result = curl_exec($ch);
	    curl_close($ch);
	}
}

#$CI =& get_instance();
if( ! function_exists('fnc_crm_get')){
	function fnc_crm_get($idticket){
		$url_api = URI_CRM . '?code=' . $idticket; #PH-20160718-003';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL			, $url_api);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER	, TRUE);
		$result = curl_exec($ch);
	    curl_close($ch);
		return json_decode($result, TRUE);
	}
}

if( ! function_exists('fnc_crm_post')){
	function fnc_crm_post($fields){
		$url_api = URI_CRM;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_ENCODING		, 'UTF-8');
		curl_setopt($ch, CURLOPT_URL			, $url_api);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER	, TRUE);
		curl_setopt($ch, CURLOPT_POST 			, count($fields));
		curl_setopt($ch, CURLOPT_POSTFIELDS		, http_build_query($fields));
		$result = curl_exec($ch);
		curl_close($ch);
		return json_decode($result, TRUE);
	}
}

if( ! function_exists('fnc_crm_put')){
	function fnc_crm_put($fields){
		$url_api = URI_CRM;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_ENCODING		, 'UTF-8');
		curl_setopt($ch, CURLOPT_URL			, $url_api);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER	, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST	, "PUT");
		curl_setopt($ch, CURLOPT_POSTFIELDS		, http_build_query($fields));
		$result = curl_exec($ch);
		curl_close($ch);
		return json_decode($result, TRUE);
	}
}