<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Update extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
        die;
    }

    function index(){
    	$agent = array();
    	$this->db->select('id,ext,id_center,id_department,id_group');
    	$this->db->where('roles', 'staff');
    	$agent = $this->db->get('tbl_accounts')->result_array();

    	foreach ($agent as $key => $value) {
    		$id = $value['id'];
    		$ext = $value['ext'];
    		$id_center = $value['id_center'];
    		$id_department = $value['id_department'];
    		$id_group = $value['id_group'];

    		echo $ext.'<br>';
    		$dbcen = $this->setdbconnect($id_center, 'center');
    		$dbgro = $this->setdbconnect($id_group, 'group');

    		$dbcen->select('fullname,mobile');
    		$dbcen->where('source', 'ccsiv1');
			$dbcen->where('status', 'called');
			$dbcen->where('start_ext', $ext);
			$data = $dbcen->get('tbl_customer')->result_array();

			if( isset($data) AND !empty($data) ){
				$insert = array();
				foreach ($data as $key => $value) {
					$fullname = $value['fullname'];
					$mobile = $value['mobile'];

					$insert[] = array(
						'fullname' => $fullname,
						'mobile' => $mobile,
						'source' => 'ccsiv1',
						'callback' => date('Y-m-d 10:00:00'),
						'id_agent' => $id,
						'start_ext' => $ext,
						'start_time' => date('Y-m-d H:i:s'),
						'end_ext' => $ext,
						'start_date' => date('Y-m-d'),
						'close_date' => date('Y-m-d'),
					);
				}
				if(isset($insert) AND !empty($insert)){
					echo "Insert:".$ext.'<br>';
					var_dump( $dbgro->insert_batch('tbl_customer', $insert) );
				}else{
					echo "Empty:".$ext.'<br>';
				}
			}
    	}
    }
}