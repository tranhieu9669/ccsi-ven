<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sale extends MX_Controller {
	public $uid_center = array();
	public $url_api = array();

	public function __construct()
    {
        parent::__construct();
        $this->uid_center = 'BBD5E283-6290-4645-A49E-2404B516BE80';
        //$this->uid_center = 'BBD5E283-6290-4645-A49E-2404B516BE80';
		$this->url_api = 'http://192.168.1.6:8084/api/app?uid_center='.$this->uid_center.'&date='.date('Y-m-d');
    }

    public function index(){
		$dataview = array();

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url_api);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$result = curl_exec($ch);
	    curl_close($ch);
	    $result = json_decode($result, TRUE);

	    if( isset($result['status']) AND $result['status']=='success' ){
	    	$dataview = $result['data'];
	    	$dataview = json_decode($dataview, TRUE);
	    	$data['data'] = $dataview;
	    }else{
	    	var_dump($result);
	    }

	    $this->load->view('sale/index', $data);
    }

    function loadcontent(){
    	$dataview = array();

    	$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url_api);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$result = curl_exec($ch);
	    curl_close($ch);
	    $result = json_decode($result, TRUE);

	    if( isset($result['status']) AND $result['status']=='success' ){
	    	$dataview = $result['data'];
	    	$dataview = json_decode($dataview, TRUE);
	    	$data['data'] = $dataview;
	    }else{
	    	var_dump($result);
	    }

	    $this->load->view('sale/salecontent', $data);
    }
}