<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {
    public $msgschedule = "";

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if( $this->_role == 'agent' ){
            # breadcrumb
            $data['breadcrumb'] = array(
                array('Home', base_url()),
                array('Danh sách khách hàng gọi', ''),
            );
        
            if ($this->input->is_ajax_request()) {
                $dataResult = array();
                $total      = 0;

                # request Param
                $request    = $_REQUEST;
                $page       = isset($request['page']) ? $request['page'] : 1;
                $pageSize   = isset($request['pageSize']) ? $request['pageSize'] : $this->grid_limit;
                $limit      = $pageSize;
                #
                
                $offset = ($page - 1) * $pageSize;
                
                $this->db->start_cache();

                $this->db->select('id, name, mobi, email, birthday, source, callval, assignby');
                $this->db->where('callval', 0);
                $this->db->where('status', 'pending');
                $this->db->where('agent', $this->_ext);
                $this->db->from('tbl_customer');

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
                '_wth_mobi'     => 120,
                '_wth_email'    => 250,
                '_wth_birthday' => 120,
                '_wth_source'   => 200,
                '_wth_assignby' => 120,
                '_wth_edit'     => 60,
            ));

            $data['content'] = 'index';
        }else{
            # breadcrumb
            $data['breadcrumb'] = array(
                array('Home', ''),
            );

            $endtime    = date('Y-m-d 23:59:59');
            //$endtime    = '2016-09-22 23:59:59';
            $starttime  = date('Y-m-d 00:00:00', strtotime($endtime . ' - 14 day'));
            //$starttime  = '2016-09-05 00:00:00';

            #pie
            $chartPie = array(
                'title'=> array(
                    'position'=> "bottom",
                    'text'=> "Biểu đồ trạng thái cuộc gọi"
                ),
                'legend'=> array(
                    'visible'=> false
                ),
                'chartArea'=> array(
                    'background'=> ""
                ),
                'seriesDefaults'=> array(
                    'labels'=> array(
                        'visible'=> true,
                        'background'=> "transparent",
                        'template'=> "#= category #\\n #= value#%"
                    )
                ),
                'series'=> array(
                    array(
                        'type'=> "pie",
                        'startAngle'=> 180,
                        'data'=> array()
                    )
                ),
                'tooltip'=> array(
                    'visible'=> true,
                    'format'=> "{0}%"
                )
            );

            $dataPie    = array();
            $totalPie   = 0;

            $this->db->start_cache();
            $this->db->select('count(*) as total, dt.`status`, st.`name`, st.`color`');
            $this->db->from('tbl_call_detail as dt');
            $this->db->join('tbl_call_status as st', 'st.`code` = dt.`status`', 'left');
            $this->db->where('created >=', $starttime);
            $this->db->where('created <', $endtime);
            $this->db->where_in('type', array('new', 'callback'));

            $this->db->stop_cache();
            $totalPie = $this->db->count_all_results();

            $this->db->group_by('dt.`status`');
            $this->db->order_by('st.`position`');
            $dataPie = $this->db->get()->result_array();
            $this->db->flush_cache();

            if( isset($dataPie) AND !empty($dataPie) AND $totalPie > 0 ){
                foreach ($dataPie as $key => $value) {
                    $cate   = ( ! empty($value['name']) ) ? $value['name'] : 'Không lưu log';
                    $val    = number_format( ( $value['total'] / $totalPie ) * 100, 2 );
                    $color  = ( ! empty($value['color']) ) ? $value['color'] : '#CC9966';

                    $chartPie['series'][0]['data'][] = array(
                        'category'  => $cate,
                        'value'     => $val,
                        'color'     => $color
                    );
                }
            }

            $data['chartPie'] = json_encode($chartPie);
            
            ##################
            $categories    = array();
            $series        = array();
            $totalColumn   = 0;
            /*
            select count(*) as total, DATE_FORMAT(calldt.created, '%Y-%m-%d') as date, calldt.`status`, callst.`name`, callst.color
            from tbl_call_detail as calldt
            left join tbl_call_status as callst on callst.`code`=calldt.`status` and callst.`status`='on'
            group by `status`, DATE_FORMAT(created, '%Y%m%d')
            order by DATE_FORMAT(created, '%Y%m%d')

            4   2016-09-05  goi_lai Gọi lại #99CC00
            1   2016-09-06  goi_lai Gọi lại #99CC00
            1   2016-09-06          
            3   2016-09-07  dong_y  Đồng ý  #00FF00
            1   2016-09-07  goi_lai Gọi lại #99CC00
            8   2016-09-07          
            1   2016-09-18          
            2   2016-09-20          
            2   2016-09-22  goi_lai Gọi lại #99CC00
            1   2016-09-22          
            */
            $dataColumn    = array();
            $this->db->start_cache();
            $this->db->select('count(*) as total, DATE(dt.created) as created, dt.`status`, st.`name`, st.`color`');
            $this->db->from('tbl_call_detail as dt');
            $this->db->join('tbl_call_status as st', 'st.`code` = dt.`status`', 'left');
            $this->db->where('created >=', $starttime);
            $this->db->where('created <', $endtime);
            $this->db->where_in('type', array('new', 'callback'));

            $this->db->stop_cache();
            $totalColumn = $this->db->count_all_results();

            $this->db->group_by('dt.`status`, DATE(dt.created)');
            $this->db->order_by('DATE(dt.created),st.`position`');
            $dataColumn = $this->db->get()->result_array();
            $this->db->flush_cache();

            # call status
            $statusCall = array();
            $this->db->select('code, name, color');
            $this->db->where('status', 'on');
            $statusCall = $this->db->get('tbl_call_status')->result_array();
            #
            $_starttime = new DateTime($starttime);
            $_endtime   = new DateTime($endtime);
            $diff = $_endtime->diff($_starttime)->days;

            $series = array();
            for ($i=0; $i <= $diff; $i++) { 
                $setday = date('Y-m-d', strtotime($starttime . ' + ' . $i . ' day'));
                array_push($categories, $setday);
            }

            /*var_dump($statusCall);
            echo '<br>';
            echo '<br>';
            var_dump($categories);
            echo '<br>';
            echo '<br>';
            var_dump($dataColumn);
            echo '<br>';
            echo '<br>';*/
            
            if( isset($statusCall) AND !empty($statusCall) ){
                foreach ($statusCall as $key => $value) {
                    $dataitem = array();
                    $code     = $value['code'];
                    $name     = $value['name'];
                    $color    = $value['color'];
                    for ($i=0; $i <= $diff; $i++) { 
                        $setday = date('Y-m-d', strtotime($starttime . ' + ' . $i . ' day'));
                        $total    = 0;
                        foreach ($dataColumn as $_key => $_value) {
                            $status     = $_value['status'];
                            $created    = $_value['created'];
                            if( $code == $status AND $created == $setday ){
                                $total  = $_value['total'];
                                unset($dataColumn[$_key]);
                            }
                        }
                        array_push($dataitem, $total);
                    }
                    $series[] = array(
                        'type'  => "column",
                        'startAngle'=> 180,
                        'color' => $color,
                        'name'  => $name,
                        'data'  => $dataitem
                    );
                }

                if( isset($dataColumn) AND ! empty($dataColumn) ){
                    $dataitem = array();
                    $name     = "Không lưu log";
                    $color    = "#55A91A";
                    for ($i=0; $i <= $diff; $i++) { 
                        $total    = 0;
                        $setday = date('Y-m-d', strtotime($starttime . ' + ' . $i . ' day'));
                        foreach ($dataColumn as $_key => $_value) {
                            $status     = $_value['status'];
                            $created    = $_value['created'];

                            if( ($created == $setday) AND (empty($status) OR !isset($status)) ){
                                $total  = $_value['total'];
                                unset($dataColumn[$_key]);
                            }
                        }
                        array_push($dataitem, $total);
                    }
                    $series[] = array(
                        'type'  => "column",
                        'startAngle'=> 180,
                        'color' => $color,
                        'name'  => $name,
                        'data'  => $dataitem
                    );
                }
            }

            /*var_dump($series);
            die;*/

            #column
            $chartColumn = array(
                'title'=> array(
                    'position'=> "bottom",
                    'text'=> "Biểu đồ trạng thái cuộc gọi chi tiết"
                ),
                'legend'=> array(
                    'visible'=> true,
                    'position'=> "top"//"bottom",
                ),
                'chartArea'=> array(
                    'background'=> ""
                ),
                'seriesDefaults'=> array(
                    'labels'=> array(
                        'visible'=> false,
                        'background'=> "transparent",
                        //template: "#= category #: \n #= value#%"
                        'template'=> "#= value#"
                    )
                ),
                'series'=> $series,
                /*'series'=> array(
                    array(
                        'type'=> "column",
                        'startAngle'=> 180,
                        'color'=> "#AD7eff",
                        'name'=> "Thành công",
                        'data'=> array(20, 15, 45, 82, 41, 60)
                    ),
                    array(
                        'type'=> "column",
                        'startAngle'=> 180,
                        'color'=> "#007eff",
                        'name'=> "Báo máy bấn",
                        'data'=> array(10, 12, 5, 22, 4, 6)
                    ),
                    array(
                        'type'=> "column",
                        'startAngle'=> 180,
                        'color'=> "#ff1c1c",
                        'name'=> "Không nghe máy",
                        'data'=> array(7, 3, 15, 12, 14, 12)
                    ),
                    array(
                        'type'=> "column",
                        'startAngle'=> 180,
                        'color'=> "#ffAF1c",
                        'name'=> "Số thuê bao",
                        'data'=> array(12, 2, 21, 4, 17, 23)
                    ),
                    array(
                        'type'=> "column",
                        'startAngle'=> 180,
                        'color'=> "#55A91A",
                        'name'=> "Không lưu log",
                        'data'=> array(1, 8, 4, 14, 7, 12)
                    ),
                ),*/
                'categoryAxis'=>array(
                    //'categories'=> array('2016-06-01', '2016-06-02', '2016-06-03', '2016-06-04', '2016-06-05', '2016-06-06'),
                    'categories'=> $categories,
                    'labels'=>array(
                        'rotation'=> -35,
                        'padding'=>array('top'=> 0)
                    )
                ),
                'tooltip'=> array(
                    'visible'=> true,
                    'format'=> "{0}"
                )
            );

            $data['chartColumn'] = json_encode($chartColumn);

            # schedule moi
            $schedule_new = array();
            $this->db->select('cus.name as cusname, cus.mobi, fra.name as franame, sch.time');
            $this->db->from('tbl_schedule as sch');
            $this->db->join('tbl_customer as cus', 'cus.id=sch.id_cus');
            $this->db->join('tbl_frametime as fra', 'fra.id=sch.id_frametime');
            $this->db->where('sch.status', 'new');
            $this->db->where('sch.created_at >', date('Y-m-d 00:00:00'));
            $this->db->order_by('sch.id', 'desc');
            $schedule_new = $this->db->get()->row_array();

            # schedule sap toi
            $schedule_now = array();
            $this->db->select('cus.name as cusname, cus.mobi, fra.name as franame, sch.time');
            $this->db->from('tbl_schedule as sch');
            $this->db->join('tbl_customer as cus', 'cus.id=sch.id_cus');
            $this->db->join('tbl_frametime as fra', 'fra.id=sch.id_frametime');
            $this->db->where_in('sch.status', array('new', 'comfirm'));
            $this->db->where('sch.time >', date('Y-m-d H:i:s'));
            $this->db->order_by('sch.time', 'asc');
            $schedule_now = $this->db->get()->row_array();

            $data['content'] = 'home';
        }
        
        $this->setlayout($data);
    }

    function autoCall($id=FALSE, $callid = FALSE, $callval= FALSE, $type='auto'){
        $this->load->helper('crm');
        #$id     = FALSE;
        #$callval= FALSE;
        #$callid = FALSE;
        $isview = FALSE;

        $msg_error  = '';
        $msg_success= '';
        $colmax     = 4;
        $rowquest   = 0;
        $detail     = array();
        # Post
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $typecall   = $this->input->post('typecall');
            
            $validation = array(
                array(
                    'field' => 'name',
                    'label' => 'Name',
                    'rules' => 'required|max_length[75]',
                ),
                array(
                    'field' => 'mobi',
                    'label' => 'Name',
                    'rules' => 'required|max_length[75]',
                ),
                /*array(
                    'field' => 'categories',
                    'label' => 'Name',
                    'rules' => 'required|max_length[75]',
                ),
                array(
                    'field' => 'product',
                    'label' => 'Name',
                    'rules' => 'required|max_length[75]',
                ),*/
                array(
                    'field' => 'status',
                    'label' => 'Name',
                    'rules' => 'required|max_length[75]',
                )
            );
            
            $uniqid     = $this->input->post('uniqid');
            $id_call    = $this->input->post('id_call');
            $id_cus     = $this->input->post('id_cus');
            $id         = $id_cus;
            $status     = $this->input->post('status');

            $this->db->select('*');
            $this->db->from('tbl_call_status');
            $this->db->where('code', $status);
            $checkstatus = $this->db->get()->row_array();

            if( isset($checkstatus['note']) AND $checkstatus['note'] > 0 ){
                $validation[] = array(
                    'field' => 'content',
                    'label' => 'Name',
                    'rules' => 'required|max_length[255]',
                );
            }

            $dateschedule = $this->input->post('dateschedule');
            $timeschedule = $this->input->post('timeschedule');
            $checkchedule = 'success';
            if( isset($checkstatus['schedule']) AND $checkstatus['schedule'] > 0 ){
                $checkchedule = $this->checkdateschedule($dateschedule, $timeschedule);
                if( $checkchedule != 'success' ){
                    $data['checkchedule'] = $checkchedule;
                }
            }

            $this->load->library('form_validation');
            $this->form_validation->set_message('required', '*');
            $this->form_validation->set_message('max_length', '*');
            $this->form_validation->set_message('is_unique', '*');
            $this->form_validation->set_message('integer', '*');

            $this->form_validation->set_rules($validation);
            if ( $this->form_validation->run() !== FALSE AND $checkchedule == 'success' ){
                $name       = $this->input->post('name');
                $gender     = $this->input->post('gender');
                $email      = $this->input->post('email');
                $mobi       = $this->input->post('mobi');
                $age        = $this->input->post('age');
                $birthday   = $this->input->post('birthday');
                $address    = $this->input->post('address');
                $location   = $this->input->post('location');
                #
                $new_fullname   = $this->input->post('new_fullname');
                $new_mobi       = $this->input->post('new_mobi');
                $new_age        = $this->input->post('new_age');
                $new_birthday   = $this->input->post('new_birthday');
                $new_gender     = $this->input->post('new_gender');
                #
                $product    = $this->input->post('product');
                $categories = $this->input->post('categories');
                $subcate    = $this->input->post('subcategories');
                $content    = $this->input->post('content');
                $duration   = $this->input->post('duration');
                $record     = $this->input->post('record');

                $datecallback = $this->input->post('datecallback');
                $timecallback = $this->input->post('timecallback');
                $callback     = $datecallback . ' ' . $timecallback;

                /*$dateschedule = $this->input->post('dateschedule');
                $timeschedule = $this->input->post('timeschedule');*/
                $schedule     = $dateschedule . ' ' . $timeschedule;

                $frametimedt = array();
                if( isset($schedule) AND ! empty($schedule) ){
                    $timecheck = explode(' ', $schedule);
                    $timecheck = '2016-01-01 ' . $timecheck[1] . ':00';
                    $this->db->select('*');
                    $this->db->where('startframe <=', $timecheck);
                    $this->db->where('endframe >', $timecheck);
                    $frametimedt = $this->db->get('tbl_frametime')->row_array();
                }
                #$frametime  = $this->input->post('frametime');

                if (date('Y-m-d H:i', strtotime($callback)) !== $callback) {
                    $callback = FALSE;
                }
                
                if (date('Y-m-d H:i', strtotime($schedule)) !== $schedule) {
                    $schedule = FALSE;
                }

                $this->db->trans_begin();

                # Update cus
                $dbupdatecus  = array(
                    'name'      => $name,
                    'gender'    => $gender,
                    #'mobi'      => $mobi,
                    'age'       => $age,
                    'email'     => $email,
                    #'birthday'  => $birthday,
                    'address'   => $address,
                    'location'  => $location
                );

                if( isset($checkstatus['introduce']) AND $checkstatus['introduce'] > 0 ){
                    if( isset($new_mobi) AND !empty($new_mobi) ){
                        $dbupdatecus['mobi2'] = $new_mobi;
                    }
                }

                $this->db->where('id', $id);
                $this->db->update('tbl_customer', $dbupdatecus);

                # Insert call detail
                $dbupdatecall = array(
                    'product'   => $product,
                    'categories'=> $categories,
                    'subcategories' => $subcate,
                    'content'   => $content,
                    'status'    => $status,
                    'duration'  => $duration,
                    'record'    => $record,
                    'updateby'  => $this->_uid,
                    'updated'   => date('Y-m-d H:i:s'),
                );


                if( isset($checkstatus['callback']) AND $checkstatus['callback'] > 0 ){
                    $dbupdatecall['callback'] = $callback;
                }

                if( isset($checkstatus['schedule']) AND $checkstatus['schedule'] > 0 ){
                    $dbupdatecall['schedule'] = $schedule;
                }

                $this->db->where('uniqid', $uniqid);
                $this->db->update('tbl_call_detail', $dbupdatecall);

                # Insert call question
                $datasms        = array();
                $dbquestion     = array();
                foreach($_POST as $key => $value){
                    if( preg_match('/question_\d/', $key) ){
                        $str_id_question= preg_replace('/question_/', '', $key);
                        $is_other       = 0;
                        if( preg_match('/question_[0-9]{1,3}_other/', $key) ){
                            $str_id_question= preg_replace('/_other/', '', $str_id_question);
                            $is_other       = 1;
                            $id_question    = intval($str_id_question);
                            $dbquestion[count($dbquestion) - 1]   = array(
                                'id_call'       => $id_call,
                                'uniqid'        => $uniqid,
                                'id_question'   => $id_question,
                                'is_other'      => $is_other,
                                'answer'        => $value,
                                'created_at'    => date('Y-m-d H:i:s'),
                            );
                        }else{
                            $id_question    = intval($str_id_question);
                            $dbquestion[]   = array(
                                'id_call'       => $id_call,
                                'uniqid'        => $uniqid,
                                'id_question'   => $id_question,
                                'is_other'      => $is_other,
                                'answer'        => $value,
                                'created_at'    => date('Y-m-d H:i:s'),
                            );
                        }
                    }

                    if(preg_match('/answer_sms_\d/', $key)){
                        $id_question    = intval( preg_replace('/answer_sms_/', '', $key) );
                        $val_sms        = $this->input->post('question_' . $id_question);

                        if( isset($val_sms) AND !empty($val_sms) ){
                            $datasms[] = array(
                                'field' => $value,
                                'val'   => $val_sms
                            );
                        }
                    }
                }

                if( isset($dbquestion) AND !empty($dbquestion) ){
                    $this->db->insert_batch('tbl_call_detail_answer', $dbquestion);
                }

                # Insert call schedule
                if( isset($checkstatus['schedule']) AND $checkstatus['schedule'] > 0 ){
                    $idcenter = $this->input->post('idcenter');
                    $centerselected = array();
                    $this->db->where('id', $idcenter);
                    $centerselected = $this->db->get('tbl_centers')->row_array();

                    $dbschedule = array(
                        'id_center'     => $centerselected['id'],
                        'uniqid'        => $uniqid,
                        'id_cus'        => $id_cus,
                        'id_call'       => $id_call,
                        'content'       => $content,
                        'id_frametime'  => ( isset($frametimedt['id']) ? $frametimedt['id'] : 0 ),
                        'time'          => $schedule,
                        'createby'      => $this->_uid,
                        'created_at'    => date('Y-m-d H:i:s'),
                        'status'        => 'new',
                    );
                    $this->db->insert('tbl_schedule', $dbschedule);
                    #CRM
                    if(IS_CRM){
                        /*$fields = array(
                            'ticketid'  => 'PH-CCSI-' . $id_cus . '-' . $id_call,
                            'des'       => $content,
                            'startdate' => $schedule,
                            'fullname'  => $name,
                            'mobile'    => $mobi,
                            'ext'       => $this->_ext,
                            'frame'     => ( isset($frametimedt['name']) ? $frametimedt['name'] : '' ),
                            'source'    => '',
                            //'allergic'  => $content,
                        );*/
                        $fields = array(
                            'ticketid'          => 'PH-CCSI-' . $id_cus . '-' . $id_call,
                            'uId_Cuahang'       => $centerselected['code'],
                            'des'               => $content,
                            'startdate'         => $schedule,
                            'fullname'          => $name,
                            'mobile'            => $mobi,
                            'ext'               => $this->_ext,
                            'frame'             => ( isset($frametimedt['name']) ? $frametimedt['name'] : '' ),
                            'source'            => '',
                            'Thoigiantao'       => date('Y-m-d H:i:s'),
                            'Thoigiancapnhat'   => date('Y-m-d H:i:s'),
                            'Trangthai'         => 1, # new
                        );

                        if( isset($datasms) AND !empty($datasms) ){
                            foreach ($datasms as $key => $value) {
                                $field  = $value['field'];
                                $val    = $value['val'];

                                $fields[$field] = $val;
                            }
                        }

                        $crm_post = fnc_crm_post($fields);

                        if( isset($crm_post['status']) AND strtolower($crm_post['status']) == 'success' ){
                            $crmlog = array(
                                'ticketid'      => 'PH-CCSI-' . $id_cus . '-' . $id_call,
                                'method'        => 'post',
                                'created_at'    => date('Y-m-d H:i:s'),
                                'created_by'    => $this->_uid
                            );

                            $this->db->insert('tbl_log_crm', $crmlog);
                        }
                    }

                    #SMS
                    if(IS_SMS){
                        $sms_cus_name = convert_codau_sang_khongdau($name);
                        $sms_cus_name = strtoupper($sms_cus_name);
                        $listname  = explode(' ', $sms_cus_name);

                        $cus_first_name = '';
                        $cus_last_name  = '';
                        if( count($listname) > 0 ){
                            $cus_last_name = $listname[count($listname) - 1];
                            $cus_first_name = trim( str_replace($cus_last_name, '', $sms_cus_name) );
                        }

                        $agent_last_name = '';
                        $agentdt = array();
                        $this->db->where('email', $this->_uid);
                        $agentdt = $this->db->get('tbl_agent')->row_array();
                        $listname  = explode(' ', $agentdt['fullname']);
                        $agent_last_name = $listname[count($listname) - 1];

                        $smslog = array(
                            'address'         => $centerselected['addresssms'],
                            'cus_first_name'  => $cus_first_name,
                            'cus_last_name'   => $cus_last_name,
                            'cus_name'        => $cus_last_name,
                            'mobile'          => $mobi,
                            'hour'            => date('H:i', strtotime($schedule)),
                            'date'            => date('d-m-Y', strtotime($schedule)),
                            'time'            => date('H:i', strtotime($frametimedt['startframe'])),
                            'agent'           => $this->_uid,
                            'agent_last_name' => $agent_last_name,
                            'ext'             => $this->_ext,
                            'mobile2'         => $this->_mobile,
                            'type'            => 'new',
                            'created_at'      => date('Y-m-d H:i:s')
                        );

                        $this->db->insert('tbl_log_sms', $smslog);
                    }
                }

                # insert new phone
                if( isset($checkstatus['introduce']) AND $checkstatus['introduce'] > 0 ){
                    if( isset($new_mobi) AND !empty($new_mobi) ){
                        $dbnewcus = array(
                            'name'      => $new_fullname,
                            'mobi'      => $new_mobi,
                            'age'       => $new_age,
                            #'birthday'  => $new_birthday,
                            'gender'    => $new_gender,
                            'address'   => $name . '-' . $mobi,
                            'source'    => $this->_uid,
                            'agent'     => $this->_ext,
                            'assignby'  => $this->_uid,
                            'status'    => 'pending',
                            'created'   => date('Y-m-d H:i:s')
                        );
                        $this->db->insert('tbl_customer', $dbnewcus);
                    }
                }

                # Trans_commit
                if ($this->db->trans_status() === FALSE){
                    $this->db->trans_rollback();
                    $msg_error      = 'Lưu thông tin cuộc gọi không thành công';
                }else{
                    $this->db->trans_commit();
                    $msg_success    = 'Lưu thông tin cuộc gọi thành công';

                    # set time next call
                    if( $type == 'auto' ){
                        $_SESSION['timenextcall'] = time() + $this->load_call;
                    }else{
                        $_SESSION['timenextcall'] = strtotime('9999-12-31 00:00:00');
                    }
                    #echo $_SESSION['timenextcall'];
                }
            }else{
                $data['msgschedule'] = $this->msgschedule;
            }

            $detail = $_POST;
        }else{
            $typecall = 'new';

            if($id AND $callid){
                $typecall   = 'callback';
                $this->db->where('callback IS NOT NULL');
                $this->db->where('id_callback', NULL);
                $this->db->where('ext', $this->_ext);
                $this->db->where('id', $callid);
                $data_callback = $this->db->get('tbl_call_detail')->row_array();

                if( isset($data_callback) AND !empty($data_callback) ){
                    $typecall   = 'callback';
                    $callval= $data_callback['callval'];
                }
            }

            if( ! $id ){
                # check callback
                $this->db->where('callback IS NOT NULL');
                $this->db->where('id_callback', NULL);
                $this->db->where('ext', $this->_ext);
                $this->db->where('callback <=', date('Y-m-d H:i:s', (strtotime(date('Y-m-d H:i:s')) + 10 * 60) ));
                $this->db->where('callback >=', date('Y-m-d H:i:s', (strtotime(date('Y-m-d H:i:s')) - 5 * 60) ));
                $this->db->order_by('callback', 'asc');
                $data_callback = $this->db->get('tbl_call_detail')->row_array();
                #echo $this->db->last_query();
                if( isset($data_callback) AND !empty($data_callback) ){
                    $typecall   = 'callback';
                    $id     = $data_callback['id_cus'];
                    $callval= $data_callback['callval'];
                    $callid = $data_callback['id'];
                }
            }
            #echo $typecall;

            if( ! $id ){
                $this->db->select('id, callval');
                $this->db->where('callval', 0);
                $this->db->where('status', 'pending');
                $this->db->where('agent', $this->_ext);
                $this->db->from('tbl_customer');
                $this->db->order_by('id', 'asc');
                $this->db->limit(1);
                $data_callnew = $this->db->get()->row_array();

                if(isset($data_callnew) AND !empty($data_callnew)){
                    $id         = $data_callnew['id'];
                    $callval    = $data_callnew['callval'];
                }
            }

            if( ! $id ){
                echo 'FALSE';
                die;
            }
            # 
            $this->db->where('id', $id);
            $this->db->where('agent', $this->_ext);
            $customer = $this->db->get('tbl_customer')->row_array();
            if( isset($customer['callval']) AND ( $customer['callval'] == $callval ) AND ! $isview ){
                $uniqid = uniqid();
                $_callval = $callval + 1;

                $this->db->where('id', $id);
                $this->db->update('tbl_customer', array('callval' => $_callval));

                $dbnewcall = array(
                    'uniqid'    => $uniqid,
                    'id_cus'    => $id,
                    'callval'   => $_callval,
                    'ext'       => $this->_ext,
                    'type'      => $typecall,
                    'createby'  => $this->_uid,
                    'created'   => date('Y-m-d H:i:s')
                );

                $this->db->insert('tbl_call_detail', $dbnewcall);
                $id_call = $this->db->insert_id();

                $detail['id_call'] = $id_call;

                # Cap nhat id callback
                if( $callid AND $callid > 0 ){
                    $this->db->where('id', $callid);
                    $this->db->update('tbl_call_detail', array('id_callback' => $id_call));
                }
            }

            ##########################
            if( ! isset($customer) OR empty($customer) ){
                echo 'FALSE';
                die;
            }
            #call
            if(IS_CALL){
                fnc_call_ob( $this->_ext, $customer['mobi'] );
            }
            # detail
            $detail             = $customer;
            $detail['id_cus']   = $id;
            $detail['typecall'] = $typecall;

            $calldetail     = array();
            $this->db->where('id_cus', $id);
            $this->db->where_in('callval', array($callval, ($callval + 1)) );
            $this->db->order_by('id', 'asc');
            $calldetail     = $this->db->get('tbl_call_detail')->result_array();

            if( isset($calldetail) AND !empty($calldetail) ){
                $id_call        = $calldetail[0]['id'];

                if( isset($calldetail[0]['id_callback']) AND ( intval($calldetail[0]['id_callback']) > 0 ) ){
                    $detail['id_call']          = $calldetail[0]['id_callback'];
                    $detail['uniqid']           = $calldetail[1]['uniqid'];
                }else{
                    $detail['id_call']          = $id_call;
                    $detail['uniqid']           = $calldetail[0]['uniqid'];
                    
                    $explodecallback            = explode(' ', $calldetail[0]['callback']);
                    $detail['datecallback']     = ( isset($explodecallback[0]) ? $explodecallback[0] : date('Y-m-d') );
                    $detail['timecallback']     = ( isset($explodecallback[1]) ? $explodecallback[1] : '09:00' );
                    #$detail['callback']         = $calldetail[0]['callback'];

                    $explodeschedule            = explode(' ', $calldetail[0]['schedule']);
                    $detail['dateschedule']     = ( isset($explodeschedule[0]) ? $explodeschedule[0] : date('Y-m-d') );
                    $detail['timeschedule']     = ( isset($explodeschedule[1]) ? $explodeschedule[1] : '09:00' );
                    #$detail['schedule']         = $calldetail[0]['schedule'];
                }

                $detail['categories']       = $calldetail[0]['categories'];
                $detail['subcategories']    = $calldetail[0]['subcategories'];
                $detail['product']          = $calldetail[0]['product'];
                $detail['status']           = $calldetail[0]['status'];
                $detail['content']          = $calldetail[0]['content'];

                $callquestion   = array();
                $this->db->where('id_call', $id_call);
                $callquestion   = $this->db->get('tbl_call_detail_answer')->result_array();

                if( isset($callquestion) AND !empty($callquestion) ){
                    foreach ($callquestion as $key => $value) {
                        if( intval($value['is_other']) > 0 ){
                            //$rowquest = $rowquest + 1;
                            $detail['question_' . $value['id_question'] . '_other']     = $value['answer'];
                        }else{
                            $detail['question_' . $value['id_question']]     = $value['answer'];
                        }
                    }
                }
            }
        }
        $data['detail']     = $detail;

        # Status
        $this->db->where('status', 'on');
        $this->db->order_by('position', 'acs');
        $this->db->order_by('id', 'acs');
        $status = $this->db->get('tbl_call_status')->result_array();
        $data['status']     = $status;

        if( count($status) > $colmax ){
            $colmax = count($status);
        }

        # Product
        $this->db->where('status', 'on');
        $product = $this->db->get('tbl_call_product')->result_array();
        $data['product']    = $product;

        # Categories
        $id_parent_cate = 0;
        if( isset($detail['categories']) AND !empty($detail['categories']) ){
            $id_parent_cate = 1;
        }

        $this->db->where('status', 'on');
        $this->db->where('id_parent', 0);
        $cateroot = $this->db->get('tbl_call_categories')->result_array();
        $data['categories'] = $cateroot;

        $catesub = array();
        if($id_parent_cate > 0){
            $this->db->where('status', 'on');
            $this->db->where('id_parent', $id_parent_cate);
            $catesub = $this->db->get('tbl_call_categories')->result_array();
        }
        $data['subcategories'] = $catesub;

        # Color
        $this->db->where('status', 'on');
        $color = $this->db->get('tbl_color')->result_array();
        $data['_color']   = $color;

        #tbl_campaign_question
        $listidquestion = array();
        $this->db->select('id_question');
        $this->db->where('status', 'on');
        $this->db->where('id_campaign', $this->_campaign);
        $list_campaign_question = $this->db->get('tbl_campaign_question')->result_array();

        if(isset($list_campaign_question) AND !empty($list_campaign_question)){
            foreach ($list_campaign_question as $key => $value) {
                array_push($listidquestion, $value['id_question']);
            }
        }
        # Question
        $question = array();
        $this->db->where('status', 'on');
        $this->db->where_in('id', $listidquestion);
        $this->db->order_by('position');
        $question = $this->db->get('tbl_call_question')->result_array();

        # Answer
        $answer = array();
        $this->db->where('status', 'on');
        $this->db->order_by('id');
        $answer = $this->db->get('tbl_call_answer')->result_array();

        $quest_ans = array();

        if( isset($question) AND !empty($question) ){
            foreach ($question as $key => $value) {
                $iddt       = $value['id'];
                $id_parent  = $value['id_parent'];
                $name       = $value['name'];
                $type       = $value['type'];
                $is_other   = $value['is_other'];
                $is_sms     = $value['is_sms'];
                $field      = $value['field'];

                if( $is_other == 'on'){
                    $rowquest = $rowquest + 1;
                }

                if( in_array($type, array('drop', 'radio', 'multicheck', 'text')) ){
                    $rowquest = $rowquest + 2;
                }else{
                    $rowquest = $rowquest + 1;
                }

                $ans = array();
                if( isset($answer) AND !empty($answer) ){
                    foreach ($answer as $_key => $_val) {
                        $_id             = $_val['id'];
                        $_id_question    = $_val['id_question'];
                        $_name           = $_val['name'];

                        if( $_id_question == $iddt ){
                            $ans[] = array(
                                'id'    => $_id,
                                'name'  => $_name
                            );
                        }
                    }
                }
                $quest_ans[] = array(
                    'id'        => $iddt,
                    'id_parent' => $id_parent,
                    'name'      => $name,
                    'type'      => $type,
                    'is_other'  => $is_other,
                    'is_sms'    => $is_sms,
                    'field'     => $field,
                    'answer'    => $ans
                );

                if( count($ans) > $colmax ){
                    $colmax = count($ans);
                }
                /*}else{
                    $quest_ans[] = array(
                        'id'        => $iddt,
                        'id_parent' => $id_parent,
                        'name'      => $name,
                        'type'      => $type,
                        'is_other'  => $is_other,
                        'is_sms'    => $is_sms,
                        'field'     => $field,
                    );
                    if( $type == 'text' ){
                        $rowquest = $rowquest + 2;
                    }else{
                        $rowquest = $rowquest + 1;
                    }
                }*/
            }
        }
        $data['quest_ans']  = $quest_ans;
        $data['rowquest']   = $rowquest;

        #history
        $callhistory    = array();
        $this->db->where('id_cus', $id);
        $this->db->order_by('id', 'desc');
        $callhistory    = $this->db->get('tbl_call_detail')->result_array();
        $data['callhistory'] = $callhistory;

        #schedule
        $callschedule   = array();
        $this->db->where('id_cus', $id);
        $this->db->order_by('id', 'desc');
        $callschedule   = $this->db->get('tbl_schedule')->result_array();
        $data['callschedule'] = $callschedule;

        #location
        $location = array();
        $this->db->where('status', 'on');
        $location = $this->db->get('tbl_location')->result_array();
        $data['location'] = $location;
        #frametime
        /*$frametime = array();
        $this->db->where('status', 'on');
        $frametime = $this->db->get('tbl_frametime')->result_array();
        $data['frametime'] = $frametime;*/
        #center
        $center = array();
        $this->db->where('status', 'on');
        $this->db->order_by('position', 'asc');
        $center = $this->db->get('tbl_centers')->result_array();
        $data['center'] = $center;
        #
        $data['msg_error']  = $msg_error;
        $data['msg_success']= $msg_success;
        $data['id']         = $id;
        $data['colmax']     = $colmax;
        $data['detail']     = $detail;
        $data['content']    = 'autocall';
        $this->setlayout($data, 'dialog');
    }

    function checkdateschedule($dateschedule, $timeschedule){
        $id_frame   = false;
        $name_frame = false;
        $startframe = false;
        $endframe   = false;
        $framedt    = array();
        $timecheck  = '2016-01-01 ' . $timeschedule . ':00';

        $this->db->select('id, name, startframe, endframe');
        $this->db->where('startframe <=', $timecheck);
        $this->db->where('endframe >', $timecheck);
        $framedt = $this->db->get('tbl_frametime')->row_array();

        if( isset($framedt) AND !empty($framedt) ){
            $id_frame   = isset($framedt['id']) ? $framedt['id'] : false;
            $name_frame = isset($framedt['name']) ? $framedt['name'] : false;
            $startframe = isset($framedt['startframe']) ? $framedt['startframe'] : false;
            $startframe = str_replace('2016-01-01', $dateschedule, $startframe);

            $endframe   = isset($framedt['endframe']) ? $framedt['endframe'] : false;
            $endframe   = str_replace('2016-01-01', $dateschedule, $endframe);
        }

        if($id_frame){
            $total = 0;
            $this->db->from('tbl_schedule');
            $this->db->where('id_frametime', $id_frame);
            $this->db->where('time >=', $startframe);
            $this->db->where('time <', $endframe);
            $this->db->where_in('status', array('new', 'comfirm'));
            $total = $this->db->count_all_results();

            $limit = 0;
            $rowframe = array();
            $this->db->select('numlimit');
            $this->db->from('tbl_schedule_limit');
            $this->db->where('date', $dateschedule);
            $this->db->where('id_frametime', $id_frame);
            $rowframe = $this->db->get()->row_array();
            if(isset($rowframe) AND !empty($rowframe)){
                $limit = $rowframe['numlimit'];
            }

            if( intval($limit) > 0 ){
                if( intval($total) >= intval($limit) ){
                    return "Lịch hẹn {$name_frame} đã đầy, không thể đặt thêm.";
                }else{
                    return 'success';
                }
            }else{
                return "Lịch hẹn chưa giới hạn, liên hệ admin.";
            }
        }else{
            return "Không tìm thấy khung giờ hẹn, liên hệ admin.";
        }
    }

    function popupclose(){
        $rcdetail = array();
        $this->db->where('ext', $this->_ext);
        $this->db->where('createby', $this->_uid);
        $this->db->where('updated is null');
        $this->db->where('updateby is null');
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $rcdetail = $this->db->get('tbl_call_detail')->row_array();

        if( isset($rcdetail) AND !empty($rcdetail) ){
            $id = $rcdetail['id'];

            $this->db->where('id', $id);
            $this->db->update('tbl_call_detail', array('closepp' => 1));
        }
    }

    function loadsubcategories($code=''){

    }

    function settimecall(){
        $_SESSION['timenextcall'] = time() + $this->load_call;
    }

    function pause(){
        $reason = $_POST['reason'];
        $data_pause = array(
            'email'     => $this->_uid,
            'uphone'    => $this->_ext,
            'pause'     => date('Y-m-d H:i:s'),
            'reason'    => $reason 
        );

        if( $this->db->insert('tbl_log_pause', $data_pause) !== FALSE ){
            $_SESSION['pausestatus'] = 10;
            $_SESSION['pausereason'] = $reason;
            echo 'SUCCESS';
        }else{
            echo 'FAIL';
        }
    }

    function unpause(){
        $this->db->where('email', $this->_uid);
        $this->db->where('uphone', $this->_ext);
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);

        $detail = $this->db->get('tbl_log_pause')->row_array();

        if( isset( $detail ) AND !empty($detail) ){
            if( ! isset($detail['unpause']) OR empty($detail['unpause']) ){
                $this->db->where('id', $detail['id']);
                $this->db->update('tbl_log_pause', array('unpause' => date('Y-m-d H:i:s')));
            }
        }

        $_SESSION['pausestatus'] = 0;
        $_SESSION['pausereason'] = '';

        if( $this->_role == 'agent' ){
            $_SESSION['timenextcall'] = time() + $this->load_call;
        }else{
            $_SESSION['timenextcall'] = strtotime('9999-12-31 00:00:00');
        }
    }

    function setrole(){
        if( isset($_POST['role']) AND !empty($_POST['role']) AND $_POST['role'] ){
            $_SESSION['roledetail'] = $_POST['role'];
            if( $_POST['role'] != 'agent' ){
                $_SESSION['timenextcall'] = strtotime('9999-12-31 00:00:00');
            }else{
                $_SESSION['timenextcall'] = time() + $this->load_call;
            }
        }else{
            $_SESSION['roledetail'] = FALSE;
        }
    }
}
