<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dial extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function index(){

    }

    function dialcall(){
        #$isDial= FALSE;
        $isDial= TRUE;
    	$urlob = 'http://192.168.1.17/ob.php';
    	$ext = $this->_ext;
    	$phone = isset($_GET['phone']) ? $_GET['phone'] : false;
        
        if( $phone ){
            if($isDial){
                $url_call = $urlob . '?txtextension=' . $ext . '&txtphonenumber=' . $phone;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL , $url_call);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER , TRUE);
                $result = curl_exec($ch);
                curl_close($ch);
            }
            $rtn = 'SUCCESS';
            echo $rtn;
        }else{
            $rtn = 'FAIL';
            echo $rtn;
        }
    }
}