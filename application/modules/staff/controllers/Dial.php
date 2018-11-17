<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dial extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function index(){
        $rtn = 'FAIL';
        $phone = isset($_GET['phoneNumber']) ? $_GET['phoneNumber'] : false;
        $sipname = $this->_sipname;
        if( $phone ){
            $ext = $this->_ext;
            $sipserver = $this->_sipserver;
            $urlob = 'http://'.$sipserver.'/ob.php';

            $url_call = $urlob . '?txtextension=' . $ext . '&txtphonenumber=' . $phone;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL , $url_call);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER , TRUE);
            $result = curl_exec($ch);
            curl_close($ch);
            $rtn = 'SUCCESS';
        }
        return $rtn;
    }

    function dialcall($phone=false, $id_link=false){
        #$isDial= FALSE;
        $isDial= TRUE;
        $sipname = $this->_sipname;

        if($sipname == 'deaura'){
            $ext = $this->_ext;
            $sipserver = $this->_sipserver;

            $urlob = 'http://'.$sipserver.'/ob.php';
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
                echo "<script>window.close();</script>";
            }else{
                $rtn = 'FAIL-Kết nối tổng đài lỗi.';
                echo $rtn;
            }
        }else{
            require_once(APPPATH.'third_party/Jwt/JWT.php');
            
            $ext_extend = $this->_ext_extend;
            $access_key = 'ZWF1cmEifQ';
            $key = $access_key;

            $expired = date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s") . ' + 300 seconds'));
            $payload = array(
                "ipphone" => $ext_extend,
                "expired" => $expired
            );

            $JWT = new JWT();
            $token = $JWT->encode($payload, $key);
            header('Location: https://c2c.caresoft.vn/deaura/c2call?token='.$token.'&number='.$phone);
        }
    }
}