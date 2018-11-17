﻿<script src="<?php echo base_url();?>assets/inputmask/jquery.maskedinput.js"></script>

<style type="text/css">
	.status-call{
		display: none;
	}

	.none{
		display: none;
	}

	.error{
		float: left;
		color: red;
	}

	#saveInformation .btn{
		padding: 5px 8px;
	}

	.popup-left{
		float: left;
		width: 0px;
	}

	.popup-right{
		float: right;
		width: 100%;
	}

	.box-infor{
		margin-top: 3px;
	}

	.box-row{
		margin-top: 1px;
	}

	.box-item{
		padding: 0px;
	}

	label{
		margin-bottom: 2px;
	}

	input[type="text"]{
		height: 28px;
	}

	.validation{
		color: red;
		float: right;
	}

	.content-call{
		border: 1px solid #CCC;
	}

	/*select checkbox*/
	.btn-group{
		width: 100%;
	}
	.btn-group > .multiselect {
		width: 100%;
		text-align: left;
		height: 26px;
		padding: 3px 8px;
	}

	.btn .caret{
		margin-top: -10px;
		float: right;
	}

	.multiselect-clear-filter{
		padding: 4px 8px;
	}

	.input-group-addon{
		border-radius: 0px;
	}

	span.k-datepicker{
    	width: 100%;
  	}

  	.panel-primary{
  		border: 0px;
  	}

  	.panel-box{
  		margin-bottom: 0px;
  	}

  	.padding-left-15{
  		padding-left: 15px;
  	}

  	.k-grid td{
  		padding:  0.2em 0.6em;
  	}

  	#action_info{
  		
  	}

  	.dial{
        float: right;
        /*margin-top: -22px;*/
        margin-right: 5px;
        margin-bottom: 5px;
    }
</style>

<form id="call_log" method="POST" action="<?php echo $_SERVER['REQUEST_URI'];?>" autocomplete="off">
	<?php
	  	if( isset($error) AND !empty($error) ){
	    	echo '<div class="form-group">';
	      		echo '<div class="col-sm-12 dialog-control">';
	        		echo '<div class="alert alert-warning" role="alert">';
	          			echo '<strong>Warning</strong> ' . $error;
	        		echo '</div>';
	      		echo '</div>';
	    	echo '</div>';
	  	}

	  	if( isset($msg_app_status) AND !empty($msg_app_status) ){
	    	echo '<div class="form-group">';
	      		echo '<div class="col-sm-12 dialog-control">';
	        		echo '<div class="alert alert-warning" role="alert">';
	          			echo '<strong>Warning</strong> ' . $msg_app_status;
	        		echo '</div>';
	      		echo '</div>';
	    	echo '</div>';
	  	}
  	?>
	<div class="container-fluid">
		<input type="hidden" name="id_call" value="<?php echo set_value_input('id_call', $customerdt);?>">
		<input type="hidden" name="id_fileup" value="<?php echo set_value_input('id_fileup', $customerdt);?>">
		<div class="col-sm-12">
			<div class="panel panel-primary panel-box">
				<div class="panel-heading">
					<h3 class="panel-title">THÔNG TIN CHUNG</h3>					
				</div>
				<div class="panel-body">
					<input type="hidden" name="id_link" value="<?php echo set_value_input('id_link', $customerdt);?>">
					<div class="col-sm-6 box-item">
						<div class="col-sm-12">
							<div class="form-group">
							    <label class="control-label col-sm-4" for="pwd">Mã nhân viên </label>
							    <div class="col-sm-8 box-row">
							      	<input type="text" class="form-control" placeholder="Mã nhân viên" value="<?php echo set_value_input('start_ext', $customerdt);?>" readonly>
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4" for="pwd">Tên nhân viên </label>
							    <div class="col-sm-8 box-row">
							      	<input type="text" class="form-control" placeholder="Tên nhân viên" value="<?php echo ($this->_agent_fname . $this->_agent_lname);?>" readonly>
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4" for="pwd">Tạo bởi </label>
							    <div class="col-sm-8 box-row">
							      	<input type="text" class="form-control" placeholder="Tạo bởi" value="<?php echo $this->_ext;?>" readonly>
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4" for="pwd">Ngày tạo </label>
							    <div class="col-sm-8 box-row">
							      	<input type="text" class="form-control" placeholder="Ngày tạo" value="<?php echo set_value_input('start_date', $customerdt);?>" readonly>
							    </div>
						  	</div>
					  	</div>
					</div>

					<div class="col-sm-6 box-item">
						<div class="col-sm-12">
							<div class="form-group">
								<input type="hidden" name="uniqid" value="<?php echo $uniqid;?>">
							    <label class="control-label col-sm-4 padding-left-15" for="pwd">Mã cuộc gọi </label>
							    <div class="col-sm-8 box-row">
							      	<input type="text" name="uniqid2" class="form-control" placeholder="Mã cuộc gọi" value="<?php echo set_value_input('code_city', $customerdt).'000001';?>" readonly>
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4 padding-left-15" for="pwd">Mã nguồn dữ liệu </label>
							    <div class="col-sm-8 box-row" style="">
							      	<input type="text" class="form-control" placeholder="Mã nguồn dữ liệu" value="" readonly>
							      	<!-- <?php echo set_value_input('source', $customerdt);?> --->
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4 padding-left-15" for="pwd">Đóng bởi </label>
							    <div class="col-sm-8 box-row">
							      	<input type="text" class="form-control" placeholder="Đóng bởi" value="<?php echo $this->_ext;?>" readonly>
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4 padding-left-15" for="pwd">Ngày đóng </label>
							    <div class="col-sm-8 box-row">
							      	<input type="text" class="form-control" placeholder="Ngày đóng" value="<?php echo set_value_input('close_date', $customerdt);?>" readonly>
							    </div>
						  	</div>
					  	</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End Information -->
	<!-- Customer -->
	<div class="container-fluid box-infor">
		<div class="col-sm-12">
			<div class="panel panel-primary panel-box">
				<div class="panel-heading">
					<?php
					if(isset($customerdt['id_fileup']) AND !empty($customerdt['id_fileup']) AND $customerdt['id_fileup'] == 99999){
						echo '<h3 class="panel-title">THÔNG TIN KHÁCH HÀNG - DEMO > 6T</h3>';
					}else{
						echo '<h3 class="panel-title">THÔNG TIN KHÁCH HÀNG</h3>';
					}
					?>
				</div>
				<div class="panel-body">
					<div class="col-sm-6 box-item">
						<div class="col-sm-12">
							<div class="form-group">
							    <label class="control-label col-sm-4" for="pwd">Tên khách hàng <span class="validation">*</span></label>
							    <div class="col-sm-8 box-row">
							      	<input type="text" name="fullname" class="form-control" placeholder="Tên khách hàng" value="<?php echo set_value_input('fullname', $customerdt);?>">
							    </div>
							    <span class="error"><?php echo form_error('fullname'); ?></span>
						  	</div>
						  	<?php
						  	$gender = 'Female';
						  	if( isset($customerdt['gender']) AND !empty($customerdt['gender']) ){
						  		$gender = $customerdt['gender'];
						  	}

						  	if( ! in_array($gender, array('Male', 'Female')) ){
						  		$gender = 'Female';
						  	}
						  	?>
						  	<div class="form-group">
							    <label class="control-label col-sm-4" for="pwd">Giới tính <span class="validation">*</span></label>
							    <div class="col-sm-8 box-row">
							    	<ul class="fieldlist" style="margin:0px;">
							            <li>
						                  	<input type="radio" name="gender" id="gender_male" value="Male" class="k-radio" <?php if($gender == 'Male'){ echo 'checked="checked"'; }?> >
						                  	<label class="k-radio-label" for="gender_male">Nam</label>
						              	</li>
						             	<li style="margin-left: 25px;">
						                  	<input type="radio" name="gender" id="gender_female" value="Female" class="k-radio" <?php if($gender == 'Female'){ echo 'checked="checked"'; }?> >
						                  	<label class="k-radio-label" for="gender_female">Nữ</label>
						              	</li>
							        </ul>
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4" for="pwd">Số điện thoại <span class="validation">*</span></label>
							    <div class="col-sm-8 box-row">
							      	<input type="text" id="mobile" name="mobile" class="form-control" placeholder="Số điện thoại" value="<?php echo set_value_input('mobile', $customerdt);?>" readonly>
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4" for="pwd">Ghi chú <span class="validation">*</span></label>
							    <div class="col-sm-8 box-row">
							      	<input type="text" name="" class="form-control" placeholder="Ghi chú" value="<?php echo set_value_input('email', $customerdt);?>">
							    </div>
						  	</div>
						  	<div class="form-group" style="display: none;">
							    <label class="control-label col-sm-4" for="pwd">Địa chỉ <span class="validation">*</span></label>
							    <div class="col-sm-8 box-row">
							      	<input type="text" name="address" class="form-control" placeholder="Địa chỉ" value="<?php echo set_value_input('address', $customerdt);?>">
							    </div>
						  	</div>
					  	</div>
					</div>
					<div class="col-sm-6 box-item">
						<div class="col-sm-12">
							<div class="form-group">
							    <label class="control-label col-sm-4 padding-left-15" for="pwd">Năm sinh <span class="validation">*</span></label>
							    <div class="col-sm-8 box-row">
							    	<input type="text" id="birthday" name="birthday" class="form-control" placeholder="yyyy" value="<?php if( isset($customerdt['birthday']) AND !empty($customerdt['birthday']) ){ echo $customerdt['birthday']; }else{ echo ( intval(date('Y')) - 99 ); } ?>">
							    </div>
							    <script type="text/javascript">
						      		$(function(){
						      			$("#birthday").mask("9999",{
						      				placeholder:"yyyy",//dd/mm/yyyy
						      				completed:function(){
					      						var datenow 	= new Date();
										  		var birthday 	= $(this).val();
										  		
										  		var age = 0;
										  		if( parseInt(datenow.getFullYear()) > parseInt(birthday) ){
										  			age = parseInt(datenow.getFullYear()) - parseInt(birthday);
										  		}
										  		$('#age').val(age);
						      				}
						      			});
						      		});
						      	</script>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4 padding-left-15" for="pwd">Tuổi <span class="validation">*</span></label>
							    <div class="col-sm-8 box-row">
							      	<input type="number" id="age" name="age" class="form-control" placeholder="Tuổi" value="<?php if( isset($customerdt['birthday']) AND !empty($customerdt['birthday']) ){ echo ( intval(date('Y')) - intval($customerdt['birthday']) ); }else{ echo '99'; } ?>" min="1" max="100" step="1" readonly>
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4 padding-left-15" for="pwd">Tỉnh/Thành phố <span class="validation">*</span></label>
							    <div class="col-sm-8 box-row">
							      	<select id="id_city" name="id_city" class="form-control-select">
							      		<option value=""> Tỉnh/Thành phố</option>
							      		<?php
							      		if( isset($city) AND !empty($city) ){
							      			foreach ($city as $key => $value) {
							      				if( isset($customerdt['id_city']) AND $customerdt['id_city'] == $value['id'] ){
							      					echo '<option value="'.$value['id'].'" selected="selected"> '.$value['name'].'</option>';
							      				}else{
							      					echo '<option value="'.$value['id'].'"> '.$value['name'].'</option>';
							      				}
							      			}
							      		}
							      		?>
							    	</select>
							    	<span class="error"><?php echo form_error('id_city'); ?></span>
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4 padding-left-15" for="pwd">Quận/Huyện <span class="validation">*</span></label>
							    <div class="col-sm-8 box-row">
							    	<select id="id_district" name="id_district" class="form-control-select">
							    		<option value=""> Quận/Huyện</option>
							    		<?php
							      		if( isset($district) AND !empty($district) ){
							      			foreach ($district as $key => $value) {
							      				echo '<option value="'.$value['id'].'"> '.$value['name'].'</option>';
							      			}
							      		}
							      		?>
							    	</select>
							    </div>
						  	</div>
					  	</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End Customer -->
	<!-- Introduce -->
	<?php if( ($this->_id_group != 54) AND !$this->_autodial) { ?>
	<div class="container-fluid box-infor">
		<div class="col-sm-12">
			<div class="panel panel-primary">
				<!-- <div class="panel-heading">
					<h3 class="panel-title">THÔNG TIN GIỚI THIỆU</h3>
				</div> -->
				<div class="panel-body">
					<div class="col-sm-12">
						<script type="text/javascript">
					        var url_introduced = "<?php echo base_url().'staff/introduced/'.$customerdt['id'];?>";
					        $(function() {
					            var column_introduced = $.parseJSON('<?php echo $column_introduced; ?>');
					            var grid_introduced_config = {
					                'target': '#grid_introduced',
					                'url': url_introduced,
					                'toolbar_template': 'toolbar_template_introduc',
					                'limit': 5,
					                'columns': [
					                    { field: "full_name", title: 'Tên đầy đủ', sortable: false,},
					                    { field: "mobile", title: 'Điện thoại', width: column_introduced._wth_mobile, sortable: false,},
					                    { field: "id_relationship", title: 'Quan hệ', width: column_introduced._wth_relationship, sortable: false,template: function(data){
						                    switch(parseInt(data.id_relationship)){
						                    	case 2:
						                    		return 'Bạn bè';
						                    		break;
					                    		case 3:
					                    			return 'Đồng nghiệp';
						                    		break;
					                    		default :
					                    			return 'Người thân';
						                    		break;
						                    }
						                }},
					                    { field: "call_status", title: 'Trạng thái', width: column_introduced._wth_status, sortable: false,},
					                ]
					            };
					            var grid_introduced = create_grid(grid_introduced_config);
					        });
					    </script>

					    <div class="col-xs-12 col-md-12" style="padding:0px;">
					        <div id="grid_introduced"></div>
					    <div>
					    <script id="toolbar_template_introduc" type="text/x-kendo-template">
					    	<div>
					            <span class="pull-left">
					                <ul class="fieldlist" style="float:left;">
					                    <li>
					                        <i class="halflings-icon list"></i>
					                        <span class="title-template-grid">Danh sách giới thiệu</span>
					                    </li>
					                </ul>
					            </span>
					            
					            <span class="pull-right">
					                <ul class="fieldlist" style="float:left;">
					                    <li>
				                        	<a _modal="dialog_infor_detail" _title="Thêm thông tin khách hàng giới thiệu" href="<?php echo base_url().'staff/introducedetail/'.$customerdt['id'];?>" class="sys_modal"><i class="fa fa-plus-square-o"></i>Thêm thông tin</a>
					                    </li>
					                </ul>
					            </span>
					        </div>
					    </script>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php }?>
	<!-- End Introduce -->
	<!-- Srarus call -->
	<div class="container-fluid box-infor">
		<div class="col-sm-12">
			<div class="panel panel-primary panel-box">
				<div class="panel-heading">
					<h3 class="panel-title">TRẠNG THÁI CUỘC GỌI</h3>
				</div>
				<div class="panel-body">
					<div class="col-sm-6 box-item">
						<div class="col-sm-12">
							<div class="form-group">
								<input type="hidden" id="hd_call_status_type" name="hd_call_status_type" value="">
							    <label class="control-label col-sm-4" for="pwd">Trạng thái chính <span class="validation">*</span></label>
							    <div class="col-sm-8 box-row">
							      	<select id="id_call_status" name="id_call_status" class="form-control-select">
							    		<option value=""> Trạng thái</option>
							    		<?php
							    		if( isset($call_status) AND !empty($call_status) ){
							    			foreach ($call_status as $key => $value) {
							    				echo '<option value="'.$value['id'].'" type='.$value['type'].'> '.$value['name'].'</option>';
							    			}
							    		}
							    		?>
							    	</select>
							    	<span class="error"><?php echo form_error('id_call_status'); ?></span>
							    </div>
						  	</div>
						  	<div class="form-group">
						  		<input type="hidden" id="hd_id_call_status_c2" name="hd_id_call_status_c2" value="0">
							    <label class="control-label col-sm-4" for="pwd">Trạng thái 2 <span id="sp_id_call_status_c2" class="validation">*</span></label>
							    <div class="col-sm-8 box-row">
							      	<select id="id_call_status_c2" name="id_call_status_c2" class="form-control-select">
							    		<option value=""> Trạng thái</option>
							    		<?php
							    		if( isset($call_status_c2) AND !empty($call_status_c2) ){
							    			foreach ($call_status_c2 as $key => $value) {
							    				echo '<option value="'.$value['id'].'"> '.$value['name'].'</option>';
							    			}
							    		}
							    		?>
							    	</select>
							    	<span class="error"><?php echo form_error('id_call_status_c2'); ?></span>
							    </div>
						  	</div>
						  	<div class="form-group none" id="divdateforcus">
							    <label class="control-label col-sm-4" for="pwd">Ngày theo <span class="validation">*</span></label>
							    <div class="col-sm-8 box-row">
							      	<input type="text" id="dateforcus" name="dateforcus" class="form-control" placeholder="dd/mm/yyyy">
							    </div>
							    <span class="error"><?php echo form_error('dateforcus'); ?></span>
							    <script type="text/javascript">
						      		$(function(){
						      			$("#dateforcus").kendoDatePicker({
								            format : 'yyyy-MM-dd',
								            min : '<?php echo date('Y-m-d');?>'
								        });
						      		});
						      	</script>
						  	</div>
					  	</div>
					</div>
					<div class="col-sm-6 box-item">
						<div class="col-sm-12">
							<div class="form-group">
								<input type="hidden" id="hd_id_call_status_c1" name="hd_id_call_status_c1" value="0">
							    <label class="control-label col-sm-4 padding-left-15" for="pwd">Trạng thái 1 <span id="sp_id_call_status_c1" class="validation">*</span></label>
							    <div class="col-sm-8 box-row">
							      	<select id="id_call_status_c1" name="id_call_status_c1" class="form-control-select">
							    		<option value=""> Trạng thái</option>
							    		<?php
							    		if( isset($call_status_c1) AND !empty($call_status_c1) ){
							    			foreach ($call_status_c1 as $key => $value) {
							    				echo '<option value="'.$value['id'].'"> '.$value['name'].'</option>';
							    			}
							    		}
							    		?>
							    	</select>
							    	<span class="error"><?php echo form_error('id_call_status_c1'); ?></span>
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4 padding-left-15" for="pwd">Mức độ ưu tiên <span class="validation">*</span></label>
							    <div class="col-sm-8 box-row">
							      	<select name="priority_level" class="form-control-select">
							    		<option value="0"> Độ ưu tiên</option>
							    		<option value="1"> Ưu tiên 1</option>
							    		<option value="2"> Ưu tiên 2</option>
							    		<option value="3"> Ưu tiên 3</option>
							    	</select>
							    </div>
						  	</div>
						  	<div class="form-group none" id="divtimeforcus">
							    <label class="control-label col-sm-4 padding-left-15" for="pwd">Theo giơ <span class="validation">*</span></label>
							    <div class="col-sm-8 box-row">
							    	<select name="timeforcus" class="form-control-select">
							    		<option value="09:00"> 09:00</option>
							    		<option value="09:30"> 09:30</option>
							    		<option value="10:00"> 10:00</option>
							    		<option value="10:30"> 10:30</option>
							    		<option value="11:00"> 11:00</option>
							    		<option value="11:30"> 11:30</option>
							    		<option value="12:00"> 12:00</option>
							    		<option value="12:30"> 12:30</option>
							    		<option value="13:00"> 13:00</option>
							    		<option value="13:30"> 13:30</option>
							    		<option value="14:00"> 14:00</option>
							    		<option value="14:30"> 14:30</option>
							    		<option value="15:00"> 15:00</option>
							    		<option value="15:30"> 15:30</option>
							    		<option value="16:00"> 16:00</option>
							    		<option value="16:30"> 16:30</option>
							    		<option value="17:00"> 17:00</option>
							    		<option value="17:30"> 17:30</option>
							    		<option value="18:00"> 18:00</option>
							    		<option value="18:30"> 18:30</option>
							    		<option value="19:00"> 19:00</option>
							    		<option value="19:30"> 19:30</option>
							    		<option value="20:00"> 20:00</option>
							    		<option value="20:30"> 20:30</option>
							    		<option value="21:00"> 21:00</option>
							    	</select>
							    </div>
							    <span class="error"><?php echo form_error('timeforcus'); ?></span>
						  	</div>
					  	</div>
					</div>
					<div class="col-sm-12 box-item">
						<div class="col-sm-2 box-item">
							<div class="form-group">
							    <label class="control-label col-sm-12" for="pwd">Ghi chú <span class="validation">*</span></label>
						  	</div>
					  	</div>
					  	<div class="col-sm-10 box-item">
					  		<div class="col-sm-12 box-row">
						      	<textarea class="control-full content-call" name="content_call"></textarea>
						    </div>
						    <span class="error"><?php echo form_error('content_call'); ?></span>
					  	</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End Srarus call -->
	<!-- Schedule -->
	<div class="container-fluid box-infor" id="appointment_info" style="display:none;">
		<div class="col-sm-12">
			<div class="panel panel-primary panel-box">
				<div class="panel-heading">
					<h3 class="panel-title">QUẢN LÝ LỊCH HẸN</h3>
				</div>
				<div class="panel-body">
					<div class="col-sm-6 box-item">
						<div class="col-sm-12">
						  	<div class="form-group">
							    <label class="control-label col-sm-4" for="pwd">Thành phố <span class="validation">*</span></label>
							    <div class="col-sm-8 box-row">
							      	<select id="id_center_city" name="id_center_city" class="form-control-select">
							      		<option value=""> Chọn thành phố</option>
							    		<?php
							      		if( isset($city) AND !empty($city) ){
							      			foreach ($city as $key => $value) {
							      				echo '<option value="'.$value['id'].'"> '.$value['name'].'</option>';
							      			}
							      		}
							      		?>
							    	</select>
							    	<span class="error"><?php echo form_error('id_center_city'); ?></span>
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4" for="pwd">Ngày hẹn <span class="validation">*</span></label>
							    <div class="col-sm-8 box-row">
							    	<input type="text" id="appointment" name="appointment" class="form-control" placeholder="dd/mm/yyyy">
							    	<span class="error"><?php echo form_error('appointment'); ?></span>
							    </div>
							    <script type="text/javascript">
						      		$(function(){
						      			$("#appointment").kendoDatePicker({
								            format : 'yyyy-MM-dd',
								            min : '<?php echo date('Y-m-d');?>'
								        });

								        $('#id_frametime').change(function(){
								        	var _start = $('#id_frametime option:selected').attr('start');
								        	$('#hd_frametime_start').val(_start);
								        });
						      		});
						      	</script>
						  	</div>
					  	</div>
					</div>
					<div class="col-sm-6 box-item">
						<div class="col-sm-12">
						  	<div class="form-group">
						  		<label class="control-label col-sm-4 padding-left-15" for="pwd">Chi nhánh <span class="validation">*</span></label>
							    <div class="col-sm-8 box-row">
							      	<select id="id_center" name="id_center" class="form-control-select">
							    		<option value=""> Chọn chi nhánh</option>
							    	</select>
							    	<span class="error"><?php echo form_error('id_center'); ?></span>
							    </div>
						  	</div>
						  	<div class="form-group">
						  		<input type="hidden" id="hd_frametime_start" name="hd_frametime_start" value="00:00">
						  		<label class="control-label col-sm-4 padding-left-15" for="pwd">Ca hẹn <span class="validation">*</span></label>
							    <div class="col-sm-8 box-row">
							      	<select id="id_frametime" name="id_frametime" class="form-control-select">
							    		<option value="" start="00:00"> Chọn ca hẹn</option>
							    	</select>
							    	<span class="error"><?php echo form_error('id_frametime'); ?></span>
							    </div>
						  	</div>
					  	</div>
					</div>
					<div class="col-sm-12 box-item">
						<div class="col-sm-2 box-item">
							<div class="form-group">
							    <label class="control-label col-sm-12" for="pwd">Ghi chú <span class="validation">*</span></label>
						  	</div>
					  	</div>
					  	<div class="col-sm-10 box-item">
					  		<div class="col-sm-12 box-row">
						      	<textarea class="control-full content-call" name="content_app"></textarea>
						    </div>
						    <span class="error"><?php echo form_error('content_app'); ?></span>
					  	</div>
					</div>
					<!-- #### -->
				</div>
			</div>
		</div>
	</div>
	<?php
	if( !isset($msg_app_status) OR empty($msg_app_status) ){
	?>
	<div class="container-fluid box-infor">
		<button type="submit" name="submit_save_data" class="btn btn btn-success dial" value="123">
			<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Save
		</button>

		<?php 
		if(isset($customerdt['mobile']) AND !empty($customerdt['mobile'])){ 
			if($this->_sipname == 'deaura' OR 1){ ?>
			<a href="<?php echo base_url().'staff/dial/'.$customerdt['mobile'];?>" class="btn btn-danger dial" target="_blank" id="dial_call">
				<span class="glyphicon glyphicon-phone-alt" aria-hidden="true"></span> Call
			</a>
			<a id="dial_call_auto" style="display:none;"></a>
			<?php }else{?>
			<a href="<?php echo 'sip:'.$customerdt['mobile'];?>" class="btn btn-danger dial" id="dial_call">
				<span class="glyphicon glyphicon-phone-alt" aria-hidden="true"></span> Call
			</a>
			<?php }
		}
		?>
	</div>
	<?php } ?>
	<!-- End Schedule -->
	<!-- History call -->
	<!-- End History call -->
</form>

<?php if($this->_autodial){ ?>
<script type="text/javascript">
	/*$(function(){
		var aopen = document.createElement("a");
		aopen.target = "_blank";
		aopen.href = "http://google.com";
		aopen.click();
	});*/
</script>
<?php }?>

<script type="text/javascript">
	$(function(){
		$( "form" ).submit(function( event ) {
			// thong tin khach hang
			var fullname = $('input[name="fullname"]').val();
			var birthday = $('input[name="birthday"]').val();

			// trang thai cuoc goi
			var id_call_status = $('#id_call_status').val();
			if( id_call_status == '' ){
				alert('Bạn chưa chọn trạng thái chính');
				return false;
			}

			var hd_id_call_status_c1 = $('#hd_id_call_status_c1').val();
			if( parseInt(hd_id_call_status_c1) > 0 ){
				var id_call_status_c1 = $('#id_call_status_c1').val();
				if( id_call_status_c1 == '' ){
					alert('Bạn chưa chọn trạng thái chi tiết 1');
					return false;
				}

				var hd_id_call_status_c2 = $('#hd_id_call_status_c2').val();
				if( parseInt(hd_id_call_status_c2) > 0 ){
					var id_call_status_c2 = $('#id_call_status_c2').val();
					if( id_call_status_c2 == '' ){
						alert('Bạn chưa chọn trạng thái chi tiết 2');
						return false;
					}
				}
			}

			var type_status = $('#hd_call_status_type').val();
			if(type_status == 'callback'){
				var dateforcus = $('#dateforcus').val();
				if(dateforcus == ''){
					alert('Bạn chưa chọn ngày theo');
					return false;
				}
			}

			var content_call = $('textarea[name="content_call"]').val();
			if(content_call == ''){
				alert('Bạn chưa nhập nội dung cuộc gọi');
				return false;
			}
			// quan ly lich hen
			if(type_status == 'appointment'){
				var id_center_city = $('#id_center_city').val();
				if(id_center_city == ''){
					alert('Bạn chưa chọn thành phố');
					return false;
				}

				var id_center = $('#id_center').val();
				if(id_center == ''){
					alert('Bạn chưa chọn trung tâm');
					return false;
				}

				var appointment = $('#appointment').val();
				if(appointment == ''){
					alert('Bạn chưa chọn ngày hẹn');
					return false;
				}

				var id_frametime = $('#id_frametime').val();
				if(id_frametime == ''){
					alert('Bạn chưa chọn ca hẹn');
					return false;
				}

				var content_app = $('textarea[name="content_app"]').val();
				if(content_app == ''){
					alert('Bạn chưa nhập nội dung lịch hẹn');
					return false;
				}
			}
			
			return true;
		});

		if($('select')){
    		$("select").select2({
        		closeOnSelect: true
    		});

    		//$('#foo').select2('enable');
    		//$('#foo').select2('disable');
    		$('#id_call_status_c1').select2('disable');
    		$('#id_call_status_c2').select2('disable');
  		}

	  	$("#id_city").change(function(){
	  		var id_city = $(this).val();
		    $.ajax({
		      url     : '<?php echo base_url();?>staff/ajax/dibyci',
		      type    : "GET",
		      data    : {'id_city':id_city},
		      success : function( data ) {
		        var obj_decode = $.parseJSON(data);
		        var district = obj_decode;
		          
		        var html_district = '<option value="" selected="selected"> Quận/Huyện</option>';
		        for(var key in district){
		          var detail  = district[key];
		          var id      = detail['id'];
		          var name    = detail['name'];
		          html_district += '<option value="' + id + '">' + name + '</option>';
		        }
		        $('#id_district').html(html_district);
		        $("#id_district").select2("val", "");
		      },

		      error   : function(msg){
		        var html_district = '<option value="" selected="selected"> Quận/Huyện</option>';
		        $('#id_district').html(html_district);
		        $("#id_district").select2("val", "");
		      }
		    });
	  	});

	  	$("#id_call_status").change(function(){
	  		var id_call_status = $(this).val();
	  		var hd_call_status_type = $('#id_call_status option:selected').attr('type');
	  		$('#hd_call_status_type').val(hd_call_status_type);

	  		if(hd_call_status_type == 'appointment'){
	  			$('#appointment_info').css('display', 'block');
	  		}else{
	  			$('#appointment_info').css('display', 'none');
	  		}

	  		if(hd_call_status_type == 'callback'){
  				$('#divdateforcus').css('display', 'block');
				$('#divtimeforcus').css('display', 'block');
  			}else{
  				$('#divdateforcus').css('display', 'none');
				$('#divtimeforcus').css('display', 'none');
  			}

	  		$.ajax({
		      	url     : '<?php echo base_url(); ?>staff/ajax/statuschild1',
		      	type    : "GET",
		      	data    : {'id_call_status':id_call_status},
		      	success : function( data ) {
		        	var obj_decode = $.parseJSON(data);
		        	var district = obj_decode;
		          
		        	var html_status_c1 = '<option value="" selected="selected"> Trạng thái</option>';
		        	var check_status_c1 = 0;
		        	for(var key in district){
		        		if( check_status_c1 < 1 ){
		        			check_status_c1 = 1;
		        		}
		          		var detail  = district[key];
		          		var id      = detail['id'];
		          		var name    = detail['name'];
		          		var type 	= detail['type'];
		          		html_status_c1 += '<option value="'+id+'" type="'+type+'">'+name+'</option>';
		        	}
		        	$('#id_call_status_c1').html(html_status_c1);
		        	$("#id_call_status_c1").select2("val", "");

		        	if( check_status_c1 > 0 ){
		        		$('#sp_id_call_status_c1').css('display','block');
	        			$('#hd_id_call_status_c1').val(1);
	        			$('#id_call_status_c1').select2('enable');
	        		}else{
	        			$('#sp_id_call_status_c1').css('display','none');
	        			$('#hd_id_call_status_c1').val(0);
	        			$('#id_call_status_c1').select2('disable');
	        		}

		        	var html_status_c2 = '<option value="" selected="selected"> Trạng thái</option>';
		        	$('#id_call_status_c2').html(html_status_c2);
		        	$("#id_call_status_c2").select2("val", "");
		        	$('#id_call_status_c2').select2('disable');
		      	},

		      	error   : function(msg){
		        	var html_status_c1 = '<option value="" selected="selected"> Trạng thái</option>';
		        	$('#id_call_status_c1').html(html_status_c1);
		        	$("#id_call_status_c1").select2("val", "");

		        	var html_status_c2 = '<option value="" selected="selected"> Trạng thái</option>';
		        	$('#id_call_status_c2').html(html_status_c2);
		        	$("#id_call_status_c2").select2("val", "");

		        	$('#hd_id_call_status_c1').val(0);
		        	$('#hd_id_call_status_c2').val(0);

		        	$('#id_call_status_c1').select2('disable');
		        	$('#id_call_status_c2').select2('disable');
		      	}
		    });
	  	});

	  	$("#id_call_status_c1").change(function(){
	  		var id_call_status 		= $("#id_call_status").val();
	  		var id_call_status_c1 	= $(this).val();

	  		if( $('#id_call_status option:selected').attr('type') == '' ){
	  			var hd_call_status_type = $('#id_call_status_c1 option:selected').attr('type');
	  			$('#hd_call_status_type').val(hd_call_status_type);

	  			if(hd_call_status_type == 'appointment'){
		  			$('#appointment_info').css('display', 'block');
		  		}else{
		  			$('#appointment_info').css('display', 'none');
		  		}

		  		if(hd_call_status_type == 'callback'){
	  				$('#divdateforcus').css('display', 'block');
					$('#divtimeforcus').css('display', 'block');
	  			}else{
	  				$('#divdateforcus').css('display', 'none');
					$('#divtimeforcus').css('display', 'none');
	  			}
	  		}

	  		$.ajax({
		      	url     : '<?php echo base_url(); ?>staff/ajax/statuschild2',
		      	type    : "GET",
		      	data    : { 'id_call_status':id_call_status, 'id_call_status_c1':id_call_status_c1 },
		      	success : function( data ) {
		        	var obj_decode = $.parseJSON(data);
		        	var district = obj_decode;
		          
		        	var html_status_c2 = '<option value="" selected="selected"> Trạng thái</option>';
		        	var check_status_c2 = 0;
		        	for(var key in district){
		        		if( check_status_c2 < 1 ){
		        			check_status_c2 = 1;
		        		}
		          		var detail  = district[key];
		          		var id      = detail['id'];
		          		var name    = detail['name'];
		          		var type 	= detail['type'];
		          		html_status_c2 += '<option value="'+id+'" type="'+type+'">'+name+'</option>';
		        	}
		        	$('#id_call_status_c2').html(html_status_c2);
		        	$("#id_call_status_c2").select2("val", "");

		        	if( check_status_c2 > 0 ){
		        		$('#sp_id_call_status_c2').css('display','block');
	        			$('#hd_id_call_status_c2').val(1);
	        			$('#id_call_status_c2').select2('enable');
	        		}else{
	        			$('#sp_id_call_status_c2').css('display','none');
	        			$('#hd_id_call_status_c2').val(0);
	        			$('#id_call_status_c2').select2('disable');
	        		}
		      	},

		      	error   : function(msg){
		        	var html_status_c2 = '<option value="" selected="selected"> Trạng thái</option>';
		        	$('#id_call_status_c2').html(html_status_c2);
		        	$("#id_call_status_c2").select2("val", "");

		        	$('#hd_id_call_status_c2').val(0);
		        	$('#id_call_status_c2').select2('disable');
		      	}
		    });
	  	});

	  	$('#id_call_status_c2').change(function(){
	  		if( $('#id_call_status_c1 option:selected').attr('type') == '' && $('#id_call_status_c1 option:selected').attr('type') == '' ){
	  			var hd_call_status_type = $('#id_call_status_c2 option:selected').attr('type');
	  			$('#hd_call_status_type').val(hd_call_status_type);

	  			if(hd_call_status_type == 'appointment'){
		  			$('#appointment_info').css('display', 'block');
		  		}else{
		  			$('#appointment_info').css('display', 'none');
		  		}

		  		if(hd_call_status_type == 'callback'){
	  				$('#divdateforcus').css('display', 'block');
					$('#divtimeforcus').css('display', 'block');
	  			}else{
	  				$('#divdateforcus').css('display', 'none');
					$('#divtimeforcus').css('display', 'none');
	  			}
	  		}
	  	});

		$("#id_center_city").change(function(){
			var id_city = $(this).val();

			$.ajax({
		      	url     : '<?php echo base_url();?>staff/ajax/cespabyci',
		      	type    : "GET",
		      	data    : { 'id_city':id_city, 'type': 'spa'  },
		      	success : function( data ) {
		        	var obj_decode = $.parseJSON(data);
		        	var center = obj_decode;
		          
		        	var html_center = '<option value="" selected="selected"> Chọn chi nhánh</option>';
		        	for(var key in center){
		          		var detail  = center[key];
		          		var id      = detail['id'];
		          		var code    = detail['code'];
		          		var name    = detail['name'];
		          		html_center += '<option value="' + id + '">' + name + '</option>';
		        	}
		        	$('#id_center').html(html_center);
		        	$("#id_center").select2("val", "");
		      	},

		      	error   : function(msg){
		        	var html_center = '<option value="" selected="selected"> Chọn chi nhánh</option>';
		        	$('#id_center').html(html_center);
		        	$("#id_center").select2("val", "");
		      	}
		    });
		});

		function frametimeappointment(){
			var id_center = $('#id_center').val();
			var dateapp = $('#appointment').val();

			if(dateapp != ''){
				var _dateapp = dateapp;

				$.ajax({
			      	url     : '<?php echo base_url();?>staff/ajax/frameapp',
			      	type    : "GET",
			      	data    : { 'id_center':id_center, 'dateapp':dateapp },
			      	success : function( data ) {
			        	var obj_decode = $.parseJSON(data);
			        	var center = obj_decode;
			          
			        	var html_frametime = '<option value="" selected="selected" start="00:00"> Chọn ca hẹn</option>';
			        	for(var key in center){
			          		var detail  = center[key];
			          		var id      = detail['id'];
			          		var name    = detail['name'];
			          		var start   = detail['start'];
			          		var end    	= detail['end'];
			          		html_frametime += '<option value="' + id + '" start="'+start+'"> ' + name + ' (' + start + '-' + end + ')</option>';
			        	}
			        	$('#id_frametime').html(html_frametime);
			        	$("#id_frametime").select2("val", "");
			      	},

			      	error   : function(msg){
			        	var html_frametime = '<option value="" selected="selected" start="00:00"> Chọn ca hẹn</option>';
			        	$('#id_frametime').html(html_frametime);
			        	$("#id_frametime").select2("val", "");
			      	}
			    });
			}
		}

		$("#appointment").change(function(){
        	frametimeappointment();
        });

		$("#id_center").change(function(){
			frametimeappointment();
		});
	});
</script>