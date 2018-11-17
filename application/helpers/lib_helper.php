<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if( ! function_exists('set_value_input')){
	function set_value_input($key = false, $data = false){
		if( !$key OR !$data ){
      return '';
    }
    if(isset($data[$key])){
      return $data[$key];
    }else{
      return '';
    }
	}
}

if( ! function_exists('subtitle')){
  function subtitle($title, $count){
    if(strlen($title) <= $count){
      return $title;
    }else{
      return substr($title, 0, $count) . '...';
    }
  }
}

if( ! function_exists('write_log'))
{
  function write_log($data = array()){
    $rtn = FALSE;
    $CI =& get_instance();
    if(isset($data) AND !empty($data)){
      if($CI->db->insert('tbl_logs', $data) !== FALSE){
        $rtn = TRUE;
      }
    }
    return $rtn;
  }
}

if( ! function_exists('convert_codau_sang_khongdau')){
  function convert_codau_sang_khongdau($str){
    $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ|ắ)/", 'a', $str);
    $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
    $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
    $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ|ớ|Õ|õ)/", 'o', $str);
    $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
    $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
    $str = preg_replace("/(đ)/", 'd', $str);
    $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
    $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
    $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
    $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
    $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
    $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
    $str = preg_replace("/(Đ)/", 'D', $str);
    return $str;
  }
}

if( ! function_exists('convert_date')){
  function convert_view_date($datetime = false){
    $str = '';
    if($datetime AND !empty($datetime)){
      $strtotime = strtotime($datetime);
      $date_now = new DateTime(date('Y-m-d H:i:s'));
      $datetime = new DateTime($datetime);

      $diff = $date_now->diff($datetime)->days;

      switch ($diff) {
              case 0:
                $str = 'Hôm nay, ' . $str;
                break;

              case 1:
                $str = 'Hôm qua, ' . $str;
                break;
              
              default:
                $dayofweek = date('w', $strtotime);
                switch ($dayofweek) {
                  case 1:
                    $str = 'Thứ hai, ';
                    break;
                  
                  case 2:
                    $str = 'Thứ ba, ';
                    break;

                  case 3:
                    $str = 'Thứ tư, ';
                    break;

                  case 4:
                    $str = 'Thứ năm, ';
                    break;

                  case 5:
                    $str = 'Thứ sáu, ';
                    break;

                  case 6:
                    $str = 'Thứ bảy, ';
                    break;

                  default:
                    $str = 'Chủ nhật, ';
                    break;
                }
                break;
            }      
    }

    $str .= $datetime->format('d-m-Y H:i:s');;
    return $str;
  }
}

if( ! function_exists('convert_price') ){
  function convert_price( $price=false, $len=3, $unit='000&nbsp;₫', $type='.' ){
    $out = '';
    if($price){
      $no = 1;
      while (strlen($price) > ($no * $len)) {
        if($no > 1){
          $out = substr($price, strlen($price) - ($no * $len), $len) . $type . $out;
        }else{
          $out = substr($price, strlen($price) - ($no * $len), $len);
        }

        $no++;
      }
      if($no > 1){
        $out = substr($price, 0, strlen($price) - (($no-1) * $len) ) . $type . $out;  
      }else{
        $out = $price;
      }
    }else{
      $out = '0';
    }
    return $out . $type . $unit;
  }
}

if( ! function_exists('check_mobile') ){
  function check_mobile($mobile){
    $rtn = FALSE;

    $mobile10 = array('086','096','097','098','032','033','034','035','036','037','038','039','-','090','093','089','070','079','077','076','078','-','091','094','088','083','084','085','081','082','-','092','-','099','052','056','058','059');

    $mobi3 = substr($mobile, 0, 3);

    if(strlen($mobile) == 10){
      if(in_array($mobi3, $mobile10)){
        $rtn = TRUE;
      }
    }

    return $rtn;
  }
}

if( ! function_exists('convert_mobile') ){
  function convert_mobile($mobile){
    $convert = array(
      '0120' => '070',
      '0121' => '079',
      '0122' => '077',
      '0126' => '076',
      '0128' => '078',
      '0123' => '083',
      '0124' => '084',
      '0125' => '085',
      '0127' => '081',
      '0129' => '082',
      '0162' => '032',
      '0163' => '033',
      '0164' => '034',
      '0165' => '035',
      '0166' => '036',
      '0167' => '037',
      '0168' => '038',
      '0169' => '039',
      '0186' => '056',
      '0188' => '058',
      '0199' => '059'
    );

    if(strlen($mobile) == 11){
      $mobi4 = substr($mobile, 0, 4);
      $mobiNew = $convert[$mobi4];
      $mobilertn = $mobiNew.substr($mobile, 4);
      return $mobilertn;
    }else{
      return $mobile;
    }
  }
}