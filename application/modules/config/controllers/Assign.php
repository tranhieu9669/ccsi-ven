<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Assign extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Cấu hình Assign', ''),
        );

        $data['content'] = 'assign/index';
    	$this->setlayout($data, 'v2/'.$this->_role);
    }
}