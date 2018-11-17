<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Monitor extends MY_Controller {
    public    $dbmonitor= false;
	public function __construct()
    {
        parent::__construct();

        # database
        $HOST = '192.168.1.19';
        $USER = 'haha';
        $PASS = '123456';
        $DBNA = 'voip_monitor';
        $PORT = 3306;

        $this->dbmonitor = new mysqli($HOST, $USER, $PASS, $DBNA, $PORT);
        if ($this->dbmonitor->connect_errno) {
            echo "Failed to connect to MySQL: (" . $this->dbmonitor->connect_errno . ") " . $this->db->connect_error;
            die;
        }
        $this->dbmonitor->set_charset("utf8");
    }

    function __destruct() {
    	if($this->dbmonitor){
            mysqli_close($this->dbmonitor);
        }
    }

    function index(){
        if( ! in_array($this->_role, array('admin','center','department','group','operator')) ){
            echo 'Xin lỗi, bạn không có quyền ở đây';
            sleep(5);
            redirect(base_url() . $this->_role);
        }

    	$sql_rp = "SELECT count(1) as total, SUM(CASE registered WHEN 1 THEN 1 ELSE 0 END) as online, SUM(CASE status WHEN 'Up' THEN 1 ELSE 0 END) as connected from `tbl_extension` WHERE `onoff`=1";
        switch ($this->_role) {
            case 'center':
                $sql_rp .= ' AND id_center='.$this->_id_center;
                break;

            case 'department':
                $sql_rp .= ' AND id_department='.$this->_id_department;
                break;

            case 'group':
                $sql_rp .= ' AND id_group='.$this->_id_group;
                break;
            
            default:
                $sql_rp .= ' AND id_center=9999';
                break;
        }
        $result_rp = $this->dbmonitor->query($sql_rp);
        $data['result_rp']   = $result_rp->fetch_assoc();
        # get data registered
        $sql_ext    = "SELECT `id`,`extension`,`ipclient`,`fullname`,`sipserver`,`registered`,`status`,`appstatus`,`duration`,`updated_at` FROM `tbl_extension` WHERE `onoff`=1 AND `registered`=1";
        switch ($this->_role) {
            case 'center':
                $sql_ext .= ' AND id_center='.$this->_id_center;
                break;

            case 'department':
                $sql_ext .= ' AND id_department='.$this->_id_department;
                break;

            case 'group':
                $sql_ext .= ' AND id_group='.$this->_id_group;
                break;
            
            default:
                # code...
                break;
        }
        $sql_ext .= ' ORDER BY extension';
        $result_ext = $this->dbmonitor->query($sql_ext);
        $data['result_ext']   = $result_ext;

    	$data['content']   = 'index';
    	$this->setlayout($data, 'v2/monitor');
    }

    function load(){
    	$id_center = isset($_GET['id_center']) ? $_GET['id_center'] : false;
        $id_department = isset($_GET['id_department']) ? $_GET['id_department'] : false;
        $id_group = isset($_GET['id_group']) ? $_GET['id_group'] : false;

        $sql_rp = "SELECT count(1) as total, SUM(CASE registered WHEN 1 THEN 1 ELSE 0 END) as online, SUM(CASE status WHEN 'Up' THEN 1 ELSE 0 END) as connected from `tbl_extension` WHERE `onoff`=1";
        switch ($this->_role) {
            case 'center':
                $sql_rp .= ' AND id_center='.$this->_id_center;
                break;

            case 'department':
                $sql_rp .= ' AND id_department='.$this->_id_department;
                break;

            case 'group':
                $sql_rp .= ' AND id_group='.$this->_id_group;
                break;
            
            default:
                # code...
                break;
        }
        $result_rp = $this->dbmonitor->query($sql_rp);
        $data['result_rp']   = $result_rp->fetch_assoc();
        # get data registered
        $sql_ext    = "SELECT `id`,`extension`,`ipclient`,`fullname`,`sipserver`,`registered`,`status`,`appstatus`,`duration`,`updated_at` FROM `tbl_extension` WHERE `onoff`=1 AND `registered`=1";
        switch ($this->_role) {
            case 'center':
                $sql_ext .= ' AND id_center='.$this->_id_center;
                break;

            case 'department':
                $sql_ext .= ' AND id_department='.$this->_id_department;
                break;

            case 'group':
                $sql_ext .= ' AND id_group='.$this->_id_group;
                break;
            
            default:
                # code...
                break;
        }
        $sql_ext .= ' ORDER BY extension';
        $result_ext = $this->dbmonitor->query($sql_ext);
        $data['result_ext']   = $result_ext;

        # view
        $data['content']   = 'load';
    	$this->setlayout($data, NULL);
    }

    function chanspy(){
    	$ext_spy = isset($_GET['ext']) ? $_GET['ext'] : false;
    	$sipserver = isset($_GET['sipserver']) ? $_GET['sipserver'] : false;
		$extension	= $this->_ext;
    	$asterisk = $sipserver;
	    $amiuser  = 'queuemetrics';
	    $amipass  = 'queuecc';
	    $amiport  = 5038;

	    $oSocket = fsockopen($asterisk, $amiport, $errnum, $errdesc) or die("Connection to host failed");
		fputs($oSocket, "Action: login\r\n");
		fputs($oSocket, "Events: off\r\n");
		fputs($oSocket, "Username: $amiuser\r\n");
		fputs($oSocket, "Secret: $amipass\r\n\r\n");
		sleep(1);

		fputs($oSocket, "Action: Originate\r\n");
		fputs($oSocket, "Channel: SIP/$extension\r\n");
		fputs($oSocket, "CallerId: $ext_spy\r\n");
		fputs($oSocket, "Application: ChanSpy\r\n");
		fputs($oSocket, "Data: SIP/$ext_spy,bq\r\n");
		//fputs($oSocket, "Data: SIP/4059,bq,w\r\n"); # nhac agent
		fputs($oSocket, "ActionID: fop2spy!SIP/$ext_spy\r\n");
		fputs($oSocket, "Async: True\r\n");
		fputs($oSocket, "Priority: 1\r\n\r\n");
		fputs($oSocket, "Action: Logoff\r\n\r\n");
		sleep(2);
		fclose($oSocket);

		echo '<html>';
		echo '<head>';
		echo '<script language="JavaScript" type="text/javascript">';
		echo 'function closeW(){';
			echo 'window.opener=self;';
			echo 'self.close();';
		echo '}';
		echo '</script>';
		echo '</head>';
		echo '<body onload="closeW()">';
		echo '</body>';
		echo '</html>';
    }
}