<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Admin extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách tài khoản', ''),
        );

        $data['content'] = 'index';
    	$this->setlayout($data, 'v2/'.$this->_role);
    }
}