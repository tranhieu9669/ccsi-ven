<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function index(){
    	$id_account = $this->_id_agent;    	

        $date_created = '';
        $this->db->select('updated_at');
        $this->db->where('id_account', $id_account);
        $this->db->order_by('id', 'ASC');
        $this->db->limit(1);
        $dt1 = $this->db->get('tbl_accounts_log')->row_array();
        $date_created = isset( $dt1['updated_at'] ) ? $dt1['updated_at'] : '';
        $data['date_created'] = $date_created;

        $city_name = '';
        if( $this->_id_city ){
            $this->db->select('name');
            $this->db->where('id', $this->_id_city);
            $dt2 = $this->db->get('tbl_city')->row_array();
            $city_name = isset($dt2['name']) ? $dt2['name'] : '';
        }
        $data['city_name'] = $city_name;

        $center_name        = '';
        if( $this->_id_center ){
            $this->db->select('name');
            $this->db->where('id', $this->_id_center);
            $dt3 = $this->db->get('tbl_centers')->row_array();
            $center_name = isset($dt3['name']) ? $dt3['name'] : '';
        }
        $data['center_name'] = $center_name;

        $department_name    = '';
        if( $this->_id_department ){
            $this->db->select('name');
            $this->db->where('id', $this->_id_department);
            $dt4 = $this->db->get('tbl_departments')->row_array();
            $department_name = isset($dt4['name']) ? $dt4['name'] : '';
        }
        $data['department_name'] = $department_name;

        $group_name    = '';
        if( $this->_id_group ){
            $this->db->select('name');
            $this->db->where('id', $this->_id_group);
            $dt5 = $this->db->get('tbl_groups')->row_array();
            $group_name = isset($dt5['name']) ? $dt5['name'] : '';
        }
        $data['group_name'] = $group_name;

    	$data['content']   = 'index';
    	$this->setlayout($data, 'v2/tmpl');
    }

    function permission(){
        $data['content']   = 'permission';
        $this->setlayout($data, 'v2/tmpl');
    }
}