<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class City extends MY_Controller {
	public function __construct()
    {
        parent::__construct();
    }

    function index(){
        $data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách Tỉnh/Thành phố', ''),
        );

        if ($this->input->is_ajax_request()) {
            $dataResult = array();
            $total      = 0;

            # request Param
            $request    = $_REQUEST;
            $page       = isset($request['page']) ? $request['page'] : 1;
            $pageSize   = isset($request['pageSize']) ? $request['pageSize'] : $this->grid_limit;
            $inputsearch= isset($request['inputsearch']) ? $request['inputsearch'] : '';
            $limit      = $pageSize;
            $offset = ($page - 1) * $pageSize;
            
            $this->db->start_cache();
            $this->db->from('tbl_city');
            $this->db->order_by('position', 'ASC');
            $this->db->order_by('id', 'ASC');

            $this->db->stop_cache();
            $total = $this->db->count_all_results();

            $this->db->limit($limit, $offset);
            $dataResult = $this->db->get()->result_array();

            $this->db->flush_cache();
            # 
            $return = array(
                'total' => $total,
                'data'  => $dataResult
            );

            echo json_encode($return);
            return;
        }

        $data["limit"] = $this->grid_limit;

        $data['column_properties'] = json_encode(array(
            '_wth_order'    => 60,
            '_wth_status'   => 60,
            '_wth_time'   	=> 180,
            '_wth_by'   	=> 120,
            '_wth_action'   => 60,
        ));

        $data['content'] = 'city/index';
    	$this->setlayout($data, 'v2/tmpl');
    }

    function detail($id=0){
        $data['breadcrumb'] = array(
            array('Home', base_url()),
            array('Danh sách cuộc gọi', ''),
        );

        $flag   = FALSE;
        if( $id AND $id > 0 ){
            $flag   = TRUE;
        }

        $success= '';
        $error  = '';
        $detail = array();

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $validation = array();

            $hdcode     = trim($this->input->post('hdcode'));
            $code       = trim($this->input->post('code'));
            $hdfirst_ext= $this->input->post('hdfirst_ext');
            $first_ext  = $this->input->post('first_ext');

            if( $flag ){
                if( $hdcode != $code ){
                    $validation[] = array(
                        'field' => 'code',
                        'label' => 'Mã trung tâm',
                        'rules' => 'required|max_length[150]|is_unique[tbl_centers.code]',
                    );
                }else{
                    $validation[] = array(
                        'field' => 'code',
                        'label' => 'Mã trung tâm',
                        'rules' => 'required|max_length[150]',
                    );
                }
            }else{
                $validation[] = array(
                    'field' => 'code',
                    'label' => 'Mã trung tâm',
                    'rules' => 'required|max_length[150]|is_unique[tbl_centers.code]',
                );
            }
            
            if( $flag ){
                if( $hdfirst_ext != $first_ext ){
                    $validation[] = array(
                        'field' => 'first_ext',
                        'label' => 'Đầu số',
                        'rules' => 'required|integer|max_length[1]|is_unique[tbl_centers.first_ext]',
                    );
                }else{
                    $validation[] = array(
                        'field' => 'first_ext',
                        'label' => 'Đầu số',
                        'rules' => 'required|integer|max_length[1]',
                    );
                }
            }else{
                $validation[] = array(
                    'field' => 'first_ext',
                    'label' => 'Đầu số',
                    'rules' => 'required|integer|max_length[1]|is_unique[tbl_centers.first_ext]',
                );
            }

            $validation[] = array(
                'field' => 'name',
                'label' => 'Tên trung tâm',
                'rules' => 'required|max_length[150]',
            );
            $validation[] = array(
                'field' => 'address',
                'label' => 'Địa chỉ trung tâm',
                'rules' => 'required|max_length[150]',
            );
            $validation[] = array(
                'field' => 'addresssms',
                'label' => 'Địa chỉ SMS',
                'rules' => 'required|max_length[150]',
            );
            $validation[] = array(
                'field' => 'position',
                'label' => 'Thứ tự',
                'rules' => 'required|integer',
            );

            $this->load->library('form_validation');
            $this->form_validation->set_message('required', '<b>%s</b> không để trống.');
            $this->form_validation->set_message('max_length', '<b>%s</b> vượt quá độ dài.');
            $this->form_validation->set_message('is_unique', '<b>%s</b> đã tồn tại trong hệ thống.');
            $this->form_validation->set_message('integer', '<b>%s</b> phải là số.');

            $this->form_validation->set_rules($validation);
            if ( $this->form_validation->run() !== FALSE ){
                $name       = trim($this->input->post('name'));
                $address    = trim($this->input->post('address'));
                $addresssms = trim($this->input->post('addresssms'));
                $addresssms = convert_codau_sang_khongdau($addresssms);
                $position   = $this->input->post('position');
                $status     = $this->input->post('status');

                $this->db->trans_begin();

                $dbcenter = array(
                    'code'      => $code,
                    'name'      => $name,
                    'address'   => $address,
                    'addresssms'=> $addresssms,
                    'first_ext' => $first_ext,
                    'position'  => $position,
                    'status'    => $status,
                    'updated_at'=> date('Y-m-d H:i:s'),
                    'updated_by'=> $this->_uid,
                );

                if( ! $flag ){
                    $this->db->insert('tbl_centers', $dbcenter);
                    $id = $this->db->insert_id();
                }else{
                    $this->db->where('id', $id);
                    $this->db->update('tbl_centers', $dbcenter);
                }

                # Luu thong tin log thay doi
                $centers_log = array(
                    'id_center' => $id,
                    'data'      => json_encode($dbcenter),
                    'updated_at'=> date('Y-m-d H:i:s'),
                    'updated_by'=> $this->_uid
                );
                $this->db->insert('tbl_centers_log', $centers_log);

                if ($this->db->trans_status() === FALSE){
                    $this->db->trans_rollback();
                    $error      = 'Cập nhật thông tin không thành công';
                }else{
                    $this->db->trans_commit();
                    $success    = 'Cập nhật thông tin thành công';
                }
            }
            $detail = $_POST;
        }

        if( $flag AND ( ! isset($detail) OR empty($detail) ) ){
            $this->db->where('id', $id);
            $detail = $this->db->get('tbl_centers')->row_array();
        }

        $data['success'] = $success;
        $data['error']   = $error;
        $data['detail']  = $detail;
        $data['content'] = 'center/detail';
        $this->setlayout($data, 'v2/dialog');
    }
}