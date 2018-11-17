<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Config extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
		if($this->_role != 'center'){
            die('Bạn không có quyền trong trang này');
        }
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Cấu hình', ''),
        );

        if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
            $strCheck = "centerShowup_";
            if(isset($_POST) AND !empty($_POST)){
                $updateShowup = array();
                foreach ($_POST as $key => $value) {
                    if (strpos($key, $strCheck) !== false) {
                        $centerId = str_replace($strCheck, '', $key);
                        $centerVal = $value;

                        $updateShowup[] = array(
                            'id' => $centerId,
                            'showup' => $centerVal
                        );
                    }
                }
                if(isset($updateShowup) AND !empty($updateShowup)){
                    $this->db->update_batch('tbl_centers', $updateShowup, 'id');
                }
            }
        }

        $centerList = array();
        $this->db->select('id, name, showup');
        $this->db->from('tbl_centers');
        $this->db->where('type', 'spa');
        $this->db->where('status', 'on');
        $this->db->order_by('position');
        $centerList = $this->db->get()->result_array();
        $data['centerList'] = $centerList;

        $data['content'] = 'config/index';
    	$this->setlayout($data, 'v2/'.$this->_role);
    }
}