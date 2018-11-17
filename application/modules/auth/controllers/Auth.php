<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
        #deaura@123 - admin@123
    }

    function get_client_ip_server() {
        $ipaddress = '';
        if ($_SERVER['HTTP_CLIENT_IP'])
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if($_SERVER['HTTP_X_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if($_SERVER['HTTP_X_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if($_SERVER['HTTP_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if($_SERVER['HTTP_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if($_SERVER['REMOTE_ADDR'])
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
     
        return $ipaddress;
    }

    function index(){
    	$msg_error = '';
        $vcontent = 'username';
        $type = 'user';
        $centerdt = array();
    	if(isset($_POST['submit_username'])){
    		$validation = array(
                array(
                    'field' => 'username',
                    'label' => 'Tên đăng nhập',
                    'rules' => 'required|max_length[25]',
                )
            );
            $this->load->library( 'form_validation' );
            $this->form_validation->set_message('required', 'Thuộc tính <b>%s</b> không để trống.');
            $this->form_validation->set_message('max_length', 'Thuộc tính <b>%s</b> vượt quá độ dài.');

            $this->form_validation->set_rules($validation);
            if ($this->form_validation->run() !== FALSE){
            	$username = $this->input->post('username');

                $dt = array();
                $this->db->select('id, multi');
                $this->db->where('username', $username);
                $this->db->limit(1);
                $dt = $this->db->get('tbl_accounts')->row_array();

                if(isset($dt) AND !empty($dt)){
                    $id = $dt['id'];
                    $multi = $dt['multi'];

                    $type = 'pass';
                    $data['username'] = $username;
                    $data['id_account'] = $id;
                    if(isset($multi) AND !empty($multi)){
                        $this->db->select('cen.id, cen.name');
                        $this->db->from('tbl_centers as cen');
                        $this->db->join('tbl_accounts_multi_config as con','con.id_center=cen.id');
                        $this->db->where('con.id_account', $id);
                        $this->db->where('cen.status', 'on');
                        $this->db->where('cen.type', 'call');
                        $centerdt = $this->db->get()->result_array();
                    }
                }else{
                    $msg_error = 'Tài khoản không tồn tại! Hãy kiểm tra lại';
                }
            }
    	}elseif(isset($_POST['submit_password'])){
            $username = $this->input->post('hdusername');
            $id_account = $this->input->post('dhid_account');
            $data['id_account'] = $id_account;
            $data['username'] = $username;

            $type = 'pass';
            $validation = array(
                array(
                    'field' => 'password',
                    'label' => 'Mật khẩu',
                    'rules' => 'required|max_length[75]',
                )
            );
            $this->load->library( 'form_validation' );
            $this->form_validation->set_message('required', 'Thuộc tính <b>%s</b> không để trống.');
            $this->form_validation->set_message('max_length', 'Thuộc tính <b>%s</b> vượt quá độ dài.');

            $this->form_validation->set_rules($validation);
            if ($this->form_validation->run() !== FALSE){
                $password = $this->input->post('password');
                $id_center = $this->input->post('id_center');
                # check 
                $authdt = array();
                $this->db->select('id,first_name,last_name,mobile,ext,ext_extend,username,hash,password,id_city,id_center,id_department,id_group,roles,autodial,typepass,lastupdatepass,sipserver,sipname');
                $this->db->from('tbl_accounts');
                $this->db->where('username', $username);
                $this->db->where('status', 'on');
                $authdt = $this->db->get()->row_array();
                if( isset($authdt) AND !empty($authdt) ){
                    $id             = $authdt['id'];
                    $hash           = $authdt['hash'];
                    $passwordcf     = $authdt['password'];
                    $typepass       = $authdt['typepass'];
                    $lastupdatepass = $authdt['lastupdatepass'];
                    $autodial       = $authdt['autodial'];
                    if($passwordcf == $this->encrypassword($password, $hash) ){
                        $data_auth = array();
                        if($id_center){
                            $_authdt = array();
                            $this->db->select('id_city,id_department, id_group');
                            $this->db->from('tbl_accounts_multi_config');
                            $this->db->where('id_account', $authdt['id']);
                            $this->db->where('id_center', $id_center);
                            $this->db->where('status', 'on');
                            $this->db->limit(1);
                            $_authdt = $this->db->get()->row_array();

                            if(isset($_authdt) AND !empty($_authdt)){
                                $data_auth = array(
                                    'id' => $authdt['id'],
                                    'first_name' => $authdt['first_name'],
                                    'last_name' => $authdt['last_name'],
                                    'mobile' => $authdt['mobile'],
                                    'ext' => $authdt['ext'],
                                    'ext_extend' => $authdt['ext_extend'],
                                    'username' => $authdt['username'],
                                    'id_city' => $_authdt['id_city'],
                                    'id_center' => $id_center,
                                    'id_department' => $_authdt['id_department'],
                                    'id_group' => $_authdt['id_group'],
                                    'roles' => $authdt['roles'],
                                    'autodial' => $autodial,
                                    'sipserver' => $authdt['sipserver'],
                                    'sipname' => $authdt['sipname'],
                                );
                            }
                        }else{
                            $data_auth = array(
                                'id' => $authdt['id'],
                                'first_name' => $authdt['first_name'],
                                'last_name' => $authdt['last_name'],
                                'mobile' => $authdt['mobile'],
                                'ext' => $authdt['ext'],
                                'ext_extend' => $authdt['ext_extend'],
                                'username' => $authdt['username'],
                                'id_city' => $authdt['id_city'],
                                'id_center' => $authdt['id_center'],
                                'id_department' => $authdt['id_department'],
                                'id_group' => $authdt['id_group'],
                                'roles' => $authdt['roles'],
                                'autodial' => $autodial,
                                'sipserver' => $authdt['sipserver'],
                                'sipname' => $authdt['sipname'],
                            );
                        }
                        // save session
                        if(isset($data_auth) AND !empty($data_auth)){
                            $this->session->set_userdata(OUTCALL_SESSION_INFO, json_encode($data_auth));
                            // check exits
                            if($this->session->userdata(OUTCALL_SESSION_INFO))
                            {
                                $dblogin = array(
                                    'username'  => $authdt['username'],
                                    'ipaddress' => $this->get_client_ip_server(),
                                    'type'      => 'login',
                                    'datetime'  => date('Y-m-d H:i:s')
                                );

                                $this->db->insert('tbl_auth_log', $dblogin);

                                redirect(base_url() . $authdt['roles']);
                            }else{
                                $msg_error  = 'Lỗi trong quá trình đăng nhập';
                            }
                        }else{
                            $msg_error  = 'Phiên làm việc kết thúc, hay thử lại';
                        }
                    }else{
                        $msg_error = 'Mật khẩu của bạn nhập vào không đúng';
                    }
                }else{
                    $msg_error = 'Tài khoản hoặc mật khẩu của bạn không đúng';
                }
            }

            if(!isset($centerdt) OR empty($centerdt)){
                $this->db->select('cen.id, cen.name');
                $this->db->from('tbl_centers as cen');
                $this->db->join('tbl_accounts_multi_config as con','con.id_center=cen.id');
                $this->db->where('con.id_account', $id_account);
                $this->db->where('cen.status', 'on');
                $this->db->where('cen.type', 'call');
                $centerdt = $this->db->get()->result_array();
            }
        }

    	$data['msg_error'] = $msg_error;
        $data['type'] = $type;
        $data['center'] = $centerdt;
    	$data['content']   = $vcontent;
    	$this->setlayout($data, NULL);
    }

    function logout(){
        $dblogout = array(
            'username'  => $this->_uid,
            'ipaddress' => $this->get_client_ip_server(),
            'type'		=> 'logout',
            'datetime'  => date('Y-m-d H:i:s')
        );

        $this->db->insert('tbl_auth_log', $dblogout);
        $this->session->unset_userdata(OUTCALL_SESSION_INFO);
        $this->session->unset_userdata(OUTCALL_SESSION_DB_AGENT);
        $this->session->sess_destroy();
        session_destroy();
        redirect(base_url() . 'auth');
    }
}