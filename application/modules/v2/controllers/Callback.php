<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Callback extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function index($id=false){
        # $id - id khach hang
        # thong tin khach hang
        $customer = array();
        //$this->db->select('');
        $this->db->where('id', $id);
        $customer = $this->db->get('tb_customer')->row_array();
        $data['customer'] = $customer;
        
    	# thong tin cuoc gọi
        $calldt = array();
        //$this->db->select('');
        $this->db->where('id_cus', $id);
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $calldt = $this->db->get('tbl_call_detail')->row_array();
        $data['calldt'] = $calldt;

    	# lich sư cuoc goi
        $history = array();
        //$this->db->select('');
        $this->db->where('id_cus', $id);
        $this->db->order_by('id');
        $history = $this->db->get('tbl_call_detail')->result_array();
        $data['history'] = $history;

    	$data['content'] = 'callback/index';
    	$this->setlayout($data, 'v2/tmpl');
    }
}