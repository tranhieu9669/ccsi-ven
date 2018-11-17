<?php
class MY_Controller extends MX_Controller {
	public $_data           = array();

    public $grid_limit      = 50;

    public $_id_city        = false;
    public $_id_center      = false;
    public $_id_department  = false;
    public $_id_group       = false;
    public $_id_agent       = false;

    public $_uid            = false;
    public $_agent_fname    = false;
    public $_agent_lname    = false;
    public $_role           = false;
    public $_ext            = false;
    public $_ext_extend     = false;
    public $_mobile         = false;
    public $_db_staff       = false;
    public $_multi          = false;

    public $_autodial       = false;

    public $_sipserver      = '192.168.1.17';
    public $_sipname        = 'deaura';
	public function __construct()
    {
        parent::__construct();
        $fetch_class    = strtolower( $this->router->fetch_class() );
        $fetch_method   = $this->router->fetch_method();
        
        if( $fetch_class != 'license' ){
            # check auth
            if(1){
                if( $this->session->userdata(OUTCALL_SESSION_INFO) ){
                    $data_auth = json_decode($this->session->userdata(OUTCALL_SESSION_INFO));

                    $this->_agent_fname     = isset($data_auth->first_name) ? $data_auth->first_name : false;
                    $this->_agent_lname     = isset($data_auth->last_name) ? $data_auth->last_name : false;
                    $this->_uid             = isset($data_auth->username) ? $data_auth->username : false;
                    $this->_ext             = isset($data_auth->ext) ? $data_auth->ext : false;
                    $this->_ext_extend      = isset($data_auth->ext_extend) ? $data_auth->ext_extend : false;
                    $this->_mobile          = isset($data_auth->mobile) ? $data_auth->mobile : false;
                    $this->_role            = isset($data_auth->roles) ? $data_auth->roles : false;

                    $this->_id_agent        = isset($data_auth->id) ? $data_auth->id : false;
                    $this->_id_group        = isset($data_auth->id_group) ? $data_auth->id_group : false;
                    $this->_id_department   = isset($data_auth->id_department) ? $data_auth->id_department : false;
                    $this->_id_center       = isset($data_auth->id_center) ? $data_auth->id_center : false;
                    $this->_id_city         = isset($data_auth->id_city) ? $data_auth->id_city : false;

                    $this->_autodial 		= isset($data_auth->autodial) ? $data_auth->autodial : false;

                    $this->_sipserver       = isset($data_auth->sipserver) ? $data_auth->sipserver : '192.168.1.17';
                    $this->_sipname         = isset($data_auth->sipname) ? $data_auth->sipname : 'deaura';

                    if( strtolower($fetch_class) == 'auth' AND strtolower($fetch_method) == 'index' ){
                        redirect(base_url().$this->_role);
                    }
                }else{
                    if( strtolower($fetch_class) != 'auth' OR strtolower($fetch_method) != 'index' ){
                        redirect(base_url() . 'auth');
                    }
                }
            }
        }

        $this->_data['nm_select'] = 'home';
    }

    function setdbconnect($id=false, $type=false){
        if( $id ){
            $host   = false; $user   = false; $pass   = ''; $dbname = false; $port   = 3306;

            $dbinfo = array();
            switch ($type) {
                case 'center':
                    $this->db->select('host,user,pass,dbname,port');
                    $this->db->where('id', $id);
                    $dbinfo = $this->db->get('tbl_centers')->row_array();
                    break;

                case 'department':
                    $this->db->select('host,user,pass,dbname,port');
                    $this->db->where('id', $id);
                    $dbinfo = $this->db->get('tbl_departments')->row_array();
                    break;

                case 'group':
                    $this->db->select('host,user,pass,dbname,port');
                    $this->db->where('id', $id);
                    $dbinfo = $this->db->get('tbl_groups')->row_array();
                    break;
                
                default: # agent
                    $this->db->select('host,user,pass,dbname,port,multi');
                    $this->db->where('id', $id);
                    $dbinfo = $this->db->get('tbl_accounts')->row_array();

                    if(isset($dbinfo['multi']) AND !empty($dbinfo['multi']) AND $dbinfo['multi']){
                        $this->db->select('host,username as user,password as pass,dbname,3306 as port');
                        $this->db->where('id_account', $id);
                        $this->db->where('id_center', $this->_id_center);
                        $dbinfo = $this->db->get('tbl_accounts_multi_config')->row_array();
                    }
                    break;
            }

            if( isset($dbinfo) AND !empty($dbinfo) ){
                $host   = (isset($dbinfo['host']) AND !empty($dbinfo['host'])) ? $dbinfo['host'] : false;
                $user   = (isset($dbinfo['user']) AND !empty($dbinfo['user'])) ? $dbinfo['user'] : false;
                $pass   = (isset($dbinfo['pass']) AND !empty($dbinfo['pass'])) ? $dbinfo['pass'] : '';
                $dbname = (isset($dbinfo['dbname']) AND !empty($dbinfo['dbname'])) ? $dbinfo['dbname'] : false;
                $port   = (isset($dbinfo['port']) AND !empty($dbinfo['port'])) ? $dbinfo['port'] : 3306;
            }

            if( $host AND $user AND $dbname ){
                $_dbcf['hostname'] = $host;
                $_dbcf['username'] = $user;
                $_dbcf['password'] = $pass;
                $_dbcf['database'] = $dbname;
                $_dbcf['dbdriver'] = 'mysqli';
                $_dbcf['port']     = $port;
                $_dbcf['dbprefix'] = '';
                $_dbcf['pconnect'] = FALSE;
                $_dbcf['db_debug'] = TRUE;
                $_dbcf['cache_on'] = FALSE;
                $_dbcf['cachedir'] = '';
                $_dbcf['char_set'] = 'utf8';
                $_dbcf['dbcollat'] = 'utf8_general_ci';
                $_dbcf['swap_pre'] = '';
                $_dbcf['encrypt'] = FALSE;
                $_dbcf['compress'] = FALSE;
                $_dbcf['stricton'] = FALSE;
                $_dbcf['failover'] = array();
                $_dbcf['save_queries'] = TRUE;

                /*$dbcon = DB($_dbcf, NULL);
                if(isset($dbcon) AND !empty($dbcon) AND $dbcon){
                    return $dbcon;
                }else{
                    return false;
                }*/

                $dbreturn = $this->load->database($_dbcf, TRUE);
                return $dbreturn;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    function autodbconnect(){
        $_dbAoto['hostname'] = '192.168.1.208';
        $_dbAoto['username'] = 'huongpm';
        $_dbAoto['password'] = 'Deaura@123';
        $_dbAoto['database'] = 'LPS';
        $_dbAoto['dbdriver'] = 'mysqli';
        $_dbAoto['port']     = $port;
        $_dbAoto['dbprefix'] = '';
        $_dbAoto['pconnect'] = FALSE;
        $_dbAoto['db_debug'] = TRUE;
        $_dbAoto['cache_on'] = FALSE;
        $_dbAoto['cachedir'] = '';
        $_dbAoto['char_set'] = 'utf8';
        $_dbAoto['dbcollat'] = 'utf8_general_ci';
        $_dbAoto['swap_pre'] = '';
        $_dbAoto['encrypt'] = FALSE;
        $_dbAoto['compress'] = FALSE;
        $_dbAoto['stricton'] = FALSE;
        $_dbAoto['failover'] = array();
        $_dbAoto['save_queries'] = TRUE;

        $dbreturn = $this->load->database($_dbAoto, TRUE);
        return $dbreturn;
    }

    function generaterandomstring($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function encrypassword($password = '', $security = '')
    {
        $password = md5($security.md5(md5($password).$security));
        return $password;
    }

    public function setlayout($data = array(), $tmpl = false){
    	if(isset($data) AND !empty($data)){
            foreach ($data as $key => $value) {
                $this->_data[$key] = $value;
            }
        }

        if($tmpl !== null){
            if( !$tmpl ){
                $tmpl = 'tmpl';
            }

            $layout = '_layouts';
            $this->load->view('../views/' . $layout . '/' . $tmpl,  $this->_data);
        }else{
            if(isset($this->_data['content'])){
                $_views = $this->_data['content'];
                unset($this->_data['content']);
                $this->load->view('../views/' . $_views,  $this->_data);
            }
        }
    }
}
?>