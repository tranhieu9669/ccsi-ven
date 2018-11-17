<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Search extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Tìm kiếm thông tin khách hàng', ''),
        );

        $data['content'] = 'search/index';
    	$this->setlayout($data, 'v2/'.$this->_role);
    }

    function updateapp(){
        $request       = $_REQUEST;
        $app_status    = isset($request['app_status']) ? $request['app_status'] : false;
        $txtsearch     = isset($request['txtsearch']) ? $request['txtsearch'] : false;

        $app_content   = false;
        switch (intval($app_status)) {
            case 5:
                $app_content = 'KH về lễ tân';
                break;

            case 6:
                $app_content = 'KH về CLS';
                break;

            case 9:
                $app_content = 'KH về soi da';
                break;
            
            default:
                $app_content = false;
                break;
        }

        if($txtsearch AND $app_content){
            $app_detail = array();
            $this->db->select('id,crm_app_status,crm_app_content');
            $this->db->where('cus_mobile', $txtsearch);
            $this->db->where('last_app', 'on');
            $this->db->where('app_status !=', 'cancel');
            $app_detail = $this->db->get('tbl_appointments')->row_array();

            if(isset($app_detail) AND !empty($app_detail)){
                $app_id = $app_detail['id'];
                $old_crm_status = $app_detail['crm_app_status'];
                $old_crm_content = $app_detail['crm_app_content'];

                $this->db->insert('logs_app_change_crm_status', array(
                    'id_app' => $app_id,
                    'old_crm_status' => $old_crm_status,
                    'old_crm_content' => $old_crm_content,
                    'new_crm_status' => $app_status,
                    'new_crm_content' => $app_content,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $this->_uid
                ));

                $this->db->where('cus_mobile', $txtsearch);
                $this->db->where('last_app', 'on');
                $this->db->update('tbl_appointments', array(
                    'crm_app_status' => $app_status,
                    'crm_app_content' => $app_content
                ));
            }
        }
    }

    function loadccsi(){
    	$dataResult    = array();
        $total         = 0;
        $request       = $_REQUEST;
        $page          = isset($request['page']) ? $request['page'] : 1;
        $pageSize      = isset($request['pageSize']) ? $request['pageSize'] : $this->grid_limit;
        $txtsearch     = isset($request['txtsearch']) ? $request['txtsearch'] : false;
        $limit         = $pageSize;
        $offset        = ($page - 1) * $pageSize;

        if($txtsearch){
            $this->db->select('cus_first_name,cus_last_name,app_datetime,app_status,agent_ext,crm_app_content,app_created_at');
            $this->db->where('cus_mobile', $txtsearch);
            $this->db->from('tbl_appointments');
            $this->db->order_by('id');
            $this->db->group_by('app_created_at');
            $dataResult = $this->db->get()->result_array();
            $total = count($dataResult);
        }

        $return = array(
            'total' => $total,
            'data'  => $dataResult,
        );
        echo json_encode($return);
    }

    function loadcrm(){
    	$dataResult    = array();
        $total         = 0;
        $request       = $_REQUEST;
        $page          = isset($request['page']) ? $request['page'] : 1;
        $pageSize      = isset($request['pageSize']) ? $request['pageSize'] : $this->grid_limit;
        $txtsearch     = isset($request['txtsearch']) ? $request['txtsearch'] : '';
        $limit         = $pageSize;
        $offset        = ($page - 1) * $pageSize;

        $url_api = 'http://192.168.1.48:8088/api/Customer?txtsearch=' . $txtsearch;
        //echo $url_api;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL , $url_api);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , TRUE);
        $result = curl_exec($ch);
        curl_close($ch);
        $dataResponse = json_decode($result, TRUE);

        if(isset($dataResponse['status']) AND strtoupper($dataResponse['status']) == 'SUCCESS'){
            $dataResult = isset($dataResponse['data']) ? json_decode($dataResponse['data'], TRUE) : array();

            $total = count($dataResult);
        }

        $return = array(
            'total' => $total,
            'data'  => $dataResult,
        );
        echo json_encode($return);
    }
}
?>