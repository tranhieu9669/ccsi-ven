<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class AssignAuto extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
		if( $this->_role != 'data' ){
            echo 'Bạn không có quyền chức năng này';
		}

		$center = array();
        $this->db->select('id,name');
        $this->db->where('status', 'on');
        $this->db->where('type', 'call');
		$this->db->order_by('position');
        $center = $this->db->get('tbl_centers')->result_array();
        $this->_data['center'] = $center;

        $source = array();
        $this->db->select('id,name');
        $this->db->select('status', 'on');
        $this->db->where('mkt', 0);
        $this->db->where('pg', 0);
        $source = $this->db->get('tbl_source')->result_array();
        $this->_data['source'] = $source;
    }

    function index(){
    	$data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Phân dữ liệu sang Auto call', ''),
        );

        if ($this->input->is_ajax_request()) {
            $dataResult = array();
            $total      = 0;
            # request Param
            $request    = $_REQUEST;
            $page       = isset($request['page']) ? $request['page'] : 1;
            $pageSize   = isset($request['pageSize']) ? $request['pageSize'] : $this->grid_limit;
            $limit      = $pageSize;
            $offset = ($page - 1) * $pageSize;

            $id_center = isset($request['id_center']) ? $request['id_center'] : false;
            $id_source = isset($request['id_source']) ? $request['id_source'] : false;
            $pg = isset($request['pg']) ? $request['pg'] : 0;
            $mkt = isset($request['mkt']) ? $request['mkt'] : 0;
            $demo6 = isset($request['demo6']) ? $request['demo6'] : false;
            $newData = isset($request['newData']) ? $request['newData'] : false;

            $this->db->start_cache();
            $this->db->select('cus.id,ifnull(cus.fullname,"NONAME") as fullname,cus.mobile,cus.id_source,sou.name as souName,cus.id_center');
            $this->db->from('tbl_customer as cus');
            $this->db->join('tbl_source as sou', 'sou.id=cus.id_source', 'left');
            $this->db->where('cus.id_campaign is null');

            if($demo6){
                $this->db->where('(cus.id_fileup is not null AND cus.id_fileup=99999)');
            }else{
                $this->db->where('(cus.id_fileup is null or cus.id_fileup < 99999)');
            }
            #$this->db->where('(cus.id_fileup is null or cus.id_fileup < 99999)');

            if($id_center){
                $this->db->where('id_center', $id_center);
            }

            if($newData){
                $this->db->where('cus.status', 'data');
            }else{
                $this->db->where('cus.status', 'redata');
            }

            if($pg AND $mkt){
                $this->db->where("(cus.focus='mkt' OR cus.focus='pg')");
            }else{
                if($pg){
                    $this->db->where('cus.focus', 'pg');
                }else{
                    $this->db->where("(cus.focus <> 'pg' OR cus.focus is null)");
                }

                if($mkt){
                    $this->db->where('cus.focus', 'mkt');
                }else{
                    $this->db->where("(cus.focus <> 'mkt' OR cus.focus is null)");
                }
            }

            if( isset($id_source) AND !empty($id_source) AND $id_source ){
                $this->db->where('cus.id_source in(' . $id_source . ')');
            }

            $this->db->stop_cache();
            $total = $this->db->count_all_results();

            $this->db->limit($limit, $offset);
            $dataResult = $this->db->get()->result_array();

            $this->db->flush_cache();

            $return = array(
                'total' => $total,
                'data'  => $dataResult,
                'query' => $this->db->last_query()
            );

            echo json_encode($return);
            return;
        }

        $data["limit"] = $this->grid_limit;

        $data['column_properties'] = json_encode(array(
            '_wth_order'    => 50,
            '_wth_mobile'   => 120,
            '_wth_source' 	=> 175,
            '_wth_time'     => 160,
            '_wth_action' 	=> 60,
        ));

    	$data['content'] = 'auto/index';
        $this->setlayout($data, 'v2/'.$this->_role);
    }

    function acassign(){
    	$dbauto = false;
    	$_dbauto['hostname'] = '192.168.1.208';
        $_dbauto['username'] = 'huongpm';
        $_dbauto['password'] = 'Deaura@123';
        $_dbauto['database'] = 'LPS';
        $_dbauto['dbdriver'] = 'mysqli';
        $_dbauto['port']     = $port;
        $_dbauto['dbprefix'] = '';
        $_dbauto['pconnect'] = FALSE;
        $_dbauto['db_debug'] = TRUE;
        $_dbauto['cache_on'] = FALSE;
        $_dbauto['cachedir'] = '';
        $_dbauto['char_set'] = 'utf8';
        $_dbauto['dbcollat'] = 'utf8_general_ci';
        $_dbauto['swap_pre'] = '';
        $_dbauto['encrypt'] = FALSE;
        $_dbauto['compress'] = FALSE;
        $_dbauto['stricton'] = FALSE;
        $_dbauto['failover'] = array();
        $_dbauto['save_queries'] = TRUE;
        $dbauto = $this->load->database($_dbauto, TRUE);
        //var_dump($dbauto); die;

    	$id_center = isset($_POST['id_center']) ? $_POST['id_center'] : false;
        $id_source = isset($_POST['id_source']) ? $_POST['id_source'] : false;
        $assign_num = isset($_POST['assign_num']) ? $_POST['assign_num'] : 1;
        $pg = isset($_POST['pg']) ? $_POST['pg'] : 0;
        $mkt = isset($_POST['mkt']) ? $_POST['mkt'] : 0;
        $demo6 = isset($_POST['demo6']) ? $_POST['demo6'] : false;
        $newData = isset($_POST['newData']) ? $_POST['newData'] : false;

        $data_log = $_POST;
        $insert_log = array(
            'type' => 'Assign',
            'data' => json_encode($data_log),
            'created_by' => 'auto-'.$this->_uid,
            'created_at' => date('Y-m-d H:i:s')
        );
        write_log($insert_log);
        # END LOG
        $rtn_str = 'error';

        $db_assign = array();
		$this->db->select('id,id_fileup,first_name,last_name,mobile,fullname,id_source,demo,sale,app,mkt,mkt_time_input,pg,pg_code,pg_time_input,focus,last_time_focus');
        $this->db->from('tbl_customer');
        $this->db->where('id_campaign is null');

        if($demo6){
            $this->db->where('(id_fileup is not null AND id_fileup=99999)');
        }else{
            $this->db->where('(id_fileup is null or id_fileup < 99999)');
        }

        if($id_center){
            $this->db->where('id_center', $id_center);
        }

        if($newData){
            $this->db->where('status', 'data');
        }else{
            $this->db->where('status', 'redata');
        }

        if($pg AND $mkt){
            $this->db->where("(focus='pg' OR mkt='mkt')");
        }else{
            if($pg){
                $this->db->where('focus', 'pg');
            }else{
                $this->db->where("(focus <> 'pg' OR focus is null)");
            }

            if($mkt){
                $this->db->where('focus', 'mkt');
            }else{
                $this->db->where("(focus <> 'mkt' OR focus is null)");
            }
        }

        if( isset($id_source) AND !empty($id_source) AND $id_source ){
            $this->db->where('id_source in(' . $id_source . ')');
        }
        $this->db->order_by('rand()');
        $this->db->limit($assign_num);
		$db_assign = $this->db->get()->result_array();
		
		$id_city = 0;
        switch ($id_center) {
            case 6: // HNI
                $id_city = 62;
                break;
            case 7: // HCM
                $id_city = 63;
                break;
            case 13: // HPG
                $id_city = 61;
                break;
            case 16: // CTO
                $id_city = 59;
                break;
             
			default:
                # code...
                break;
        }

		//var_dump($db_assign); die;
		if( isset($db_assign) AND !empty($db_assign) ){
            $dbcenter = $this->setdbconnect($id_center, 'center');
            #var_dump($dbcenter); die;
			foreach ($db_assign as $key => $value) {
				$id = $value['id'];
				$mobile = $value['mobile'];
                $mobile = preg_replace("/[^0-9]/", "", $mobile);
                $fullname = $value["fullname"];
                $fullname = str_replace("'", '', $fullname);
                $fullname = str_replace('"', '', $fullname);
                $fullname = str_replace ('\\', '', $fullname);
				//$id_city = $value['id_city'];
				$id_source = $value['id_source'];
				//$source = $value['source'];
                $demo = $value['demo'];
                $sale = $value['sale'];
                $app = $value['app'];

                if($demo > 0 OR $sale > 0 OR $app > 0){
                    #echo '3'.$mobile."\n\r";
                    $this->db->where('id', $id);
                    $this->db->update('tbl_customer', array(
                        'id_center' => $id_center,
                        'id_call_status' => 9,
                        'id_call_status_c1' => 13,
                        'id_call_status_c2' => null,
                        'status' => 'assign',
                    ));
                }else{
                    $dbauto->where('id_link', $id);
                    $dbauto->where('mobile', $mobile);
                    $dbauto->delete('tbl_customer');

                    $autoInsert = array(
                        'id_link' => $id,
                        'id_script' => 1,
                        'uniqid' => uniqid('',true),
                        'fullname' => $fullname,
                        'mobile' => $mobile,
                        'id_city' => $id_city,
                        'id_location' => $id_city,
                        'id_source' => $id_source,
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => 'autoManual',
                        'start_date' => date('Y-m-d'),
                        'action_date' => date('Y-m-d')
                    );
                    #echo '1'."\n\r";
                    if($dbauto->insert('tbl_customer', $autoInsert) !== false){
                        #echo '2'.$mobile."\n\r";
                        // update center
                        if($dbcenter){
                            $dbcenter->where('mobile', $mobile);
                            $dbcenter->delete('tbl_customer');
                        }
                        // update global
                        $this->db->where('id', $id);
                        #$this->db->where('mobile', $mobile);
                        $this->db->update('tbl_customer', array(
                            'id_center' => $id_center,
                            'id_department' => 999,
                            'id_group' => 999,
                            'id_agent' => 0,
                            'id_call_status' => null,
                            'id_call_status_c1' => null,
                            'id_call_status_c2' => null,
                            #'focus' => 'tele',
                            'status' => 'assign',
                        ));
                    }
                }
			}
		}
		$rtn_str = 'success';
		echo $rtn_str;
    }
}