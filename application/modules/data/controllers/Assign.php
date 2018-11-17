<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Assign extends MY_Controller {
	private $dbhni = false;
	private $dbhcm = false;
	private $dbcto = false;
	private $dbhpg = false;

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
        #$this->db->where('mkt', 0);
        #$this->db->where('pg', 0);
        $source = $this->db->get('tbl_source')->result_array();
        $this->_data['source'] = $source;
        ############################################
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

    	$data['content'] = 'assign/index';
        $this->setlayout($data, 'v2/'.$this->_role);
    }

    function acassign(){
    	$status = "fail";

    	$id_center = isset($_POST['id_center']) ? $_POST['id_center'] : false;
        $id_source = isset($_POST['id_source']) ? $_POST['id_source'] : false;
		$assign_num = isset($_POST['assign_num']) ? $_POST['assign_num'] : 1;
        $pg = isset($_POST['pg']) ? $_POST['pg'] : 0;
        $mkt = isset($_POST['mkt']) ? $_POST['mkt'] : 0;
        $demo6 = isset($_POST['demo6']) ? $_POST['demo6'] : false;
        $newData = isset($_POST['newData']) ? $_POST['newData'] : false;

        $cusId_city = 0;
        switch ($id_center) {
            case 6: #HNI
                $cusId_city = 62;
                break;

            case 7: #HCM
                $cusId_city = 63;
                break;

            case 13: #HPG
                $cusId_city = 61;
                break;

            case 16: #CTO
                $cusId_city = 59;
                break;
            
            default:
                break;
        }
        
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

        $dataResult = $this->db->get()->result_array();
        /*var_dump($dataResult);
        echo $this->db->last_query();
        die;*/

		if(isset($dataResult) AND !empty($dataResult)){
			$dbCenter = $this->setdbconnect($id_center, 'center');
            #var_dump($dbCenter); die;
            $time_date = date('Y-m-d H:i:s');
            $action_date = date('Y-m-d');

			foreach ($dataResult as $key => $value) {
				$id = $value["id"];
				$id_fileup = $value["id_fileup"];
				$first_name = $value["first_name"];
				$last_name = $value["last_name"];
				$mobile = $value["mobile"];
                $mobile = preg_replace("/[^0-9]/", "", $mobile);
				$fullname = $value["fullname"];
                $fullname = str_replace("'", '', $fullname);
                $fullname = str_replace('"', '', $fullname);
                $fullname = str_replace ('\\', '', $fullname);
				$id_source = $value["id_source"];
				$demo = $value["demo"];
				$sale = $value["sale"];
				$app = $value["app"];
                $mkt = $value["mkt"];
                $mkt_time_input = $value["mkt_time_input"];
                $pg = $value["pg"];
                $pg_code = $value["pg_code"];
                $pg_time_input = $value["pg_time_input"];
                $focus = $value["focus"];
                $last_time_focus = $value["last_time_focus"];

                /*
                before: '%match'
                after: 'match%'
                both: '%match%'
                */
                #$dbCenter->like('mobile', $mobile, 'both');
                $dbCenter->where('mobile', $mobile);
                $dbCenter->delete('tbl_customer');

                $insertCenter = array(
                    'id_link' => $id,
                    'id_fileup' => $id_fileup,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'fullname' => $fullname,
                    'mobile' => $mobile,
                    #'id_call_status' => 9,
                    #'id_call_status_c1' => 13,
                    'id_call_status_c2' => null,
                    #'status' => 'assign'
                    'id_city' => $cusId_city,
                    'id_source' => $id_source,
                    'last_assign_by' => 'ccsiManual',
                    #'status' => 'new',
                    'id_location' => $cusId_city,
                    'created_at' => $time_date,
                    'created_by' => 'ccsiManual',
                    'start_date' => $action_date,
                    'mkt' => $mkt,
                    'mkt_time_input' => $mkt_time_input,
                    'pg' => $pg,
                    'pg_code' => $pg_code,
                    'pg_time_input' => $pg_time_input,
                    'focus' => $focus,
                    'last_time_focus' => $last_time_focus,
                );

                if($demo > 0 OR $sale > 0 OR $app > 0){
                    $insertCenter["id_call_status"] = 9;
                    $insertCenter["id_call_status_c1"] = 13;
                    if(!isset($id_fileup) OR empty($id_fileup) OR intval($id_fileup) < 99999){
                        $insertCenter["status"] = 'assign';
                    }else{
                        $insertCenter["status"] = 'new';    
                    }
                }else{
                    $insertCenter["id_call_status"] = null;
                    $insertCenter["id_call_status_c1"] = null;
                    $insertCenter["status"] = 'new';
                }

                if($dbCenter->insert('tbl_customer', $insertCenter) !== false){
                    $updateSuccess = array(
                        'mobile' => $mobile,
                        'id_call_status_c2' => null,
                        'status' => 'assign'
                    );

                    if($demo > 0 OR $sale > 0 OR $app > 0){
                        $updateSuccess["id_call_status"] = 9;
                        $updateSuccess["id_call_status_c1"] = 13;
                    }else{
                        $updateSuccess["id_call_status"] = null;
                        $updateSuccess["id_call_status_c1"] = null;
                    }
                    
                    $this->db->where('id', $id);
                    $this->db->where('mobile', $mobile);
                    $this->db->update('tbl_customer', $updateSuccess);
                }
			}

            $status = "success";
            #var_dump($dataResult);
		}

    	echo $status;
    }
}