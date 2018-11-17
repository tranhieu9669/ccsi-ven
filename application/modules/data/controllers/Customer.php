<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Customer extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
	if( $this->_role != 'data' ){
		echo 'B?n không có quy?n trong ch?c nang này';
	}
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Data', ''),
        );

        $data['content'] = 'customer/index';
        $this->setlayout($data, 'v2/'.$this->_role);
    }
}