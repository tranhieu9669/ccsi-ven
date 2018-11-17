<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Appointment extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function index($id=false){
    	# call detail
    	# history


    	$data['content'] = 'appointment/index';
    	$this->setlayout($data, 'v2/tmpl');
    }
}