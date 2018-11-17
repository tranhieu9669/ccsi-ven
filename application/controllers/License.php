<?php defined('BASEPATH') OR exit('No direct script access allowed');
class License extends MY_Controller {
    public function index(){
        $msg_error = '';
        $data['msg_error'] = $msg_error;
        $this->load->view('license');
    }
}
?>