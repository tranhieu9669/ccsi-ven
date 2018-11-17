<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Update extends MY_Controller {
	public function __construct(){
        parent::__construct();
    }

    public function index(){
    	$file_path   = APPPATH . 'uploads/Migrate_Data_20161023.csv';
    	//echo $file_path;
    	if( ($handle = fopen($file_path, "r")) !== FALSE ){
    		$datacus    = array();

    		$isdata = false;
    		$index = 0;
    		while ( ($data = fgetcsv($handle)) !== FALSE) {
    			if( ! $isdata ){
                    $isdata = true;
                    continue;
                }
                $index = $index + 1;
                echo $index . ' - ';
    			$data = array_values($data);
    			$name 	= $data[0];
    			$mobi 	= $data[1];
    			$gender = $data[2];
    			$source = $data[3];
    			$agent 	= $data[4];
    			$created= $data[5];
    			$time1  = explode(' ', $created);
    			$date1  = explode('/', $time1[0]);
    			$created= '2016-' . $date1[0] . '-' . $date1[1] . ' ' . $time1[1] . ':00';
    			$updated= $data[6];
    			$time2  = explode(' ', $updated);
    			$date2  = explode('/', $time2[0]);
    			$updated= '2016-' . $date2[0] . '-' . $date2[1] . ' ' . $time2[1] . ':00';
    			$status = 'pending';
    			$comment= $data[8];

    			$customer = array(
    				'name'		=> $name,
					'mobi'		=> $mobi,
					'gender'	=> $gender,
					'source'	=> $source,
					'callval'	=> 1,
					'agent'		=> $agent,
					'created'	=> $created,
					'updated'	=> $updated,
					'status'	=> $status
				);
    			
    			var_dump($customer); die;

    			$this->db->insert('tbl_customer', $customer);
    			echo 'mobi : ' . $mobi;

    			$id_cus = $this->db->insert_id();

    			$uniqid = uniqid();

    			$call_detail = array(
    				'uniqid'   => $uniqid,
    				'id_cus'   => $id_cus,
    				'callval'  => 1,
    				'ext'	   => $agent,
    				'content'  => $comment,
    				'status'   => 'goi_lai',
    				'callback' => '2016-10-24 10:00:00',
    				'createby' => 'huongpm1',
    				'created'  => date('Y-m-d H:i:s'),
    				'type'	   => 'new'
				);

				$this->db->insert('tbl_call_detail', $call_detail);
				echo ' detail : ' . $uniqid . '<br>==========================================================<br>';

				if( ($index % 500) == 0 ){
					sleep(5);
				}
    		}
    	}
    }
}