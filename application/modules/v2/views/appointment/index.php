<style type="text/css">
	.status-call{
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
  	.padding-left-15{
  		padding-left: 15px;
  	}

  	.k-grid td{
  		padding:  0.2em 0.6em;
  	}
</style>

<form id="call_log" method="POST" action="<?php echo $_SERVER['REQUEST_URI'];?>" autocomplete="off">
	<!-- Information -->
	<div class="container-fluid">
		<div class="col-sm-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title">THÔNG TIN CHUNG</h3>
				</div>
				<div class="panel-body">
					<input type="hidden" name="id_cus_call" value="<?php echo set_value_input('id', $customerdt);?>">
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
							      	<input type="text" class="form-control" placeholder="Tên nhân viên" value="<?php echo set_value_input('start_ext', $customerdt);?>" readonly>
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4" for="pwd">Tạo bởi </label>
							    <div class="col-sm-8 box-row">
							      	<input type="text" class="form-control" placeholder="Tạo bởi" value="<?php echo set_value_input('created_by', $customerdt);?>" readonly>
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4" for="pwd">Ngày tạo </label>
							    <div class="col-sm-8 box-row">
							      	<input type="text" class="form-control" placeholder="Ngày tạo" value="<?php echo set_value_input('created_at', $customerdt);?>" readonly>
							    </div>
						  	</div>
					  	</div>
					</div>

					<div class="col-sm-6 box-item">
						<div class="col-sm-12">
							<div class="form-group">
							    <label class="control-label col-sm-4 padding-left-15" for="pwd">Mã cuộc gọi </label>
							    <div class="col-sm-8 box-row">
							      	<input type="text" name="uniqid" class="form-control" placeholder="Mã cuộc gọi" value="<?php echo $uniqid;?>" readonly>
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4 padding-left-15" for="pwd">Mã nguồn dữ liệu </label>
							    <div class="col-sm-8 box-row">
							      	<input type="text" class="form-control" placeholder="Mã nguồn dữ liệu" value="<?php echo set_value_input('source', $customerdt);?>" readonly>
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4 padding-left-15" for="pwd">Đóng bởi </label>
							    <div class="col-sm-8 box-row">
							      	<input type="text" class="form-control" placeholder="Đóng bởi" value="<?php echo set_value_input('last_updated_by', $customerdt);?>" readonly>
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4 padding-left-15" for="pwd">Ngày đóng </label>
							    <div class="col-sm-8 box-row">
							      	<input type="text" class="form-control" placeholder="Ngày đóng" value="<?php echo set_value_input('updated_at', $customerdt);?>" readonly>
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
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title">THÔNG TIN KHÁCH HÀNG</h3>
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
						  	if( isset($customerdt['']) AND !empty($customerdt['']) ){
						  		$gender = $customerdt['gender'];
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
							    <label class="control-label col-sm-4" for="pwd">Số điện thoại </label>
							    <div class="col-sm-8 box-row">
							      	<input type="text" name="mobile" class="form-control" placeholder="Số điện thoại" value="<?php echo set_value_input('mobile', $customerdt);?>" readonly>
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4" for="pwd">Địa chỉ </label>
							    <div class="col-sm-8 box-row">
							      	<input type="text" name="address" class="form-control" placeholder="Địa chỉ" value="<?php echo set_value_input('address', $customerdt);?>">
							    </div>
						  	</div>
					  	</div>
					</div>
					<div class="col-sm-6 box-item">
						<div class="col-sm-12">
							<div class="form-group">
							    <label class="control-label col-sm-4 padding-left-15" for="pwd">Ngày sinh </label>
							    <div class="col-sm-8 box-row">
							    	<input type="text" id="birthday" name="birthday" class="form-control date-picker" placeholder="Ngày sinh" value="<?php echo set_value_input('birthday', $customerdt);?>">
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4 padding-left-15" for="pwd">Tuổi </label>
							    <div class="col-sm-8 box-row">
							      	<input type="number" name="age" class="form-control" placeholder="Tuổi" value="<?php echo set_value_input('age', $customerdt);?>" min="1" max="100" step="1">
							      	<span class="error"><?php echo form_error('age'); ?></span>
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
							      				echo '<option value="'.$value['id'].'"> '.$value['name'].'</option>';
							      			}
							      		}
							      		?>
							    	</select>
							    	<span class="error"><?php echo form_error('id_city'); ?></span>
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4 padding-left-15" for="pwd">Quận/Huyện </label>
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
	<div class="container-fluid box-infor">
		<div class="col-sm-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title">THÔNG TIN GIỚI THIỆU</h3>
				</div>
				<div class="panel-body">
					<div class="col-sm-12">
						<script type="text/javascript">
					        var url_introduced = "<?php echo base_url() . $customerdt['id'];?>-introduced";
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
					                    { field: "id_relationship", title: 'Quan hệ', width: column_introduced._wth_relationship, sortable: false,},
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
				                        	<a _modal="dialog_infor_detail" _title="Thêm thông tin khách hàng giới thiệu" href="<?php echo base_url()?>1-addintroduc" class="sys_modal"><i class="fa fa-plus-square-o"></i>Thêm thông tin</a>
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
	<!-- End Introduce -->
	<!-- Srarus call -->
	<div class="container-fluid box-infor">
		<div class="col-sm-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title">TRẠNG THÁI CUỘC GỌI</h3>
				</div>
				<div class="panel-body">
					<div class="col-sm-6 box-item">
						<div class="col-sm-12">
							<div class="form-group">
							    <label class="control-label col-sm-4" for="pwd">Trạng thái chính </label>
							    <div class="col-sm-8 box-row">
							      	<select id="id_call_status" name="id_call_status" class="form-control-select">
							    		<option value=""> Trạng thái</option>
							    		<?php
							    		if( isset($call_status) AND !empty($call_status) ){
							    			foreach ($call_status as $key => $value) {
							    				echo '<option value="'.$value['id'].'"> '.$value['name'].'</option>';
							    			}
							    		}
							    		?>
							    	</select>
							    	<span class="error"><?php echo form_error('id_call_status'); ?></span>
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4" for="pwd">Ngày theo </label>
							    <div class="col-sm-8 box-row">
							      	<input type="text" name="dateforcus" class="form-control date-picker" placeholder="Ngày theo">
							    </div>
							    <span class="error"><?php echo form_error('dateforcus'); ?></span>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4" for="pwd">Theo giơ </label>
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
						  	<div class="form-group">
							    <label class="control-label col-sm-4" for="pwd">Mức độ ưu tiên </label>
							    <div class="col-sm-8 box-row">
							      	<select name="priority" class="form-control-select">
							    		<option value=""> Độ ưu tiên</option>
							    	</select>
							    </div>
						  	</div>
					  	</div>
					</div>
					<div class="col-sm-6 box-item">
						<div class="col-sm-12">
							<div class="form-group">
							    <label class="control-label col-sm-4 padding-left-15" for="pwd">Trạng thái 1 </label>
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
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4 padding-left-15" for="pwd">Trạng thái 2 </label>
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
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4 padding-left-15" for="pwd">Chuyển team </label>
							    <div class="col-sm-8 box-row">
							      	<select id="id_group" name="id_group" class="form-control-select">
							    		<option value=""> Chọn team</option>
							    		<?php
							    		if(isset($group) AND !empty($group)){
							    			foreach ($group as $key => $value) {
							    				echo '<option value="'.$value['id'].'"> '.$value['name'].'</option>';
							    			}
							    		}
							    		?>
							    	</select>
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4 padding-left-15" for="pwd">Chuyển nhân viên </label>
							    <div class="col-sm-8 box-row">
							      	<select id="id_agent" name="id_agent" class="form-control-select">
							    		<option value=""> Chọn nhân viên</option>
							    		<?php
							    		if(isset($agent) AND !empty($agent)){
							    			foreach ($agent as $key => $value) {
							    				echo '<option value="'.$value['id'].'"> '.$value['full_name'].' ('.$value['username'].')'.'</option>';
							    			}
							    		}
							    		?>
							    	</select>
							    </div>
						  	</div>
					  	</div>
					</div>
					<div class="col-sm-12 box-item">
						<div class="col-sm-2 box-item">
							<div class="form-group">
							    <label class="control-label col-sm-12" for="pwd">Ghi chú </label>
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
	<div class="container-fluid box-infor"> <!-- style="display:none;" -->
		<div class="col-sm-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title">QUẢN LÝ LỊCH HẸN</h3>
				</div>
				<div class="panel-body">
					<div class="col-sm-6 box-item">
						<div class="col-sm-12">
							<div class="form-group">
							    <label class="control-label col-sm-4" for="pwd">Sale team </label>
							    <div class="col-sm-8 box-row">
							      	<select class="form-control-select">
							    		<option value=""> Nhóm hỗ trợ</option>
							    		<?php
							    		if(isset($group) AND !empty($group)){
							    			foreach ($group as $key => $value) {
							    				echo '<option value="'.$value['id'].'"> '.$value['name'].'</option>';
							    			}
							    		}
							    		?>
							    	</select>
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4" for="pwd">Sản phẩm </label>
							    <div class="col-sm-8 box-row">
							      	<select class="form-control-select">
							    		<option value=""> Sản phẩm</option>
							    	</select>
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4" for="pwd">Ngày hẹn </label>
							    <div class="col-sm-8 box-row">
							    	<input type="text" name="appointment" class="form-control date-picker" placeholder="Ngày theo">
							    </div>
							    <span class="error"><?php echo form_error('appointment'); ?></span>
						  	</div>
						  	<div class="form-group">
						  		<label class="control-label col-sm-4" for="pwd">Chi nhánh </label>
							    <div class="col-sm-8 box-row">
							      	<select id="id_center" name="id_center" class="form-control-select">
							    		<option value=""> Chọn chi nhánh</option>
							    	</select>
							    </div>
							    <span class="error"><?php echo form_error('id_center'); ?></span>
						  	</div>
					  	</div>
					</div>
					<div class="col-sm-6 box-item">
						<div class="col-sm-12">
							<div class="form-group">
							    <label class="control-label col-sm-4 padding-left-15" for="pwd">BC </label>
							    <div class="col-sm-8 box-row">
							      	<select class="form-control-select">
							    		<option value=""> Trạng thái BC</option>
							    	</select>
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4 padding-left-15" for="pwd">Tình trạng khách </label>
							    <div class="col-sm-8 box-row">
							      	<select class="form-control-select">
							    		<option value=""> Tình trạng khách</option>
							    	</select>
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4 padding-left-15" for="pwd">Thành phố </label>
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
							    </div>
							    <span class="error"><?php echo form_error('id_center_city'); ?></span>
						  	</div>
						  	<div class="form-group">
						  		<label class="control-label col-sm-4 padding-left-15" for="pwd">Ca hẹn </label>
							    <div class="col-sm-8 box-row">
							      	<select id="id_frametime" name="id_frametime" class="form-control-select">
							    		<option value=""> Chọn ca hẹn</option>
							    	</select>
							    </div>
							    <span class="error"><?php echo form_error('id_frametime'); ?></span>
						  	</div>
					  	</div>
					</div>
					<div class="col-sm-12 box-item">
						<div class="col-sm-2 box-item">
							<div class="form-group">
							    <label class="control-label col-sm-12" for="pwd">Ghi chú </label>
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
	<!-- End Schedule -->
	<div class="container-fluid box-infor"> <!-- style="display:none;" -->
		<div class="col-sm-12">
			<button type="submit" name="submit_save_data" class="btn btn-primary" value="123"> Lưu thông tin</button>
		</div>
	</div>
	<!-- History call -->
	<div class="container-fluid box-infor">
		<div class="col-sm-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title">LỊCH SỬ CUỘC GỌI</h3>
				</div>
				<div class="panel-body">
					<div class="col-sm-12">
						<script type="text/javascript">
					        var url_history = "<?php echo base_url();?>account.html";
					        $(function() {
					            var column_history_properties = $.parseJSON('<?php echo $column_history; ?>');
					            var grid_history_config = {
					                'target': '#grid_history',
					                'url': url_history,
					                'limit': 5,
					                //'toolbar_template': 'toolbar_template_history',
					                'columns': [
					                    { field: "email", title: 'Email', width: column_history_properties._wth_uid, sortable: false,},
					                    { field: "uphone", title: 'Máy lẻ', width: column_history_properties._wth_uphone, sortable: false,},
					                    { field: "mobile", title: 'Điện thoại', width: 100, sortable: false,},
					                    { field: "passwordcall", title: 'Mật khẩu Voip', width: column_history_properties._wth_pass, sortable: false,},
					                    { field: "group", title: 'Nhóm', width: column_history_properties._wth_group, sortable: false,},
					                    { field: "is_role", title: 'Quyền', width: column_history_properties._wth_role, sortable: false,},
					                    <?php if( in_array($this->_role, array("sysadmin", "admin")) ){ ?>
					                    { field: "edit", title: 'Sửa', sortable: false  , width: column_history_properties._wth_edit, template: function(data){
					                        var html = '<a _modal="dialog_infor_detail" _title="Sửa thông tin tài khoản" href="<?php echo base_url();?>account/acc-' + data.id + '-edit.html" class="sys_modal btn btn-primary btn_edit btn-default">';
					                        html = html + 'Sửa';
					                        html = html + '</a>';
					                        return html;
					                    }},
					                    <?php } ?>
					                ]
					            };
					            var grid_history = create_grid(grid_history_config);
					        });
					    </script>

					    <div class="col-xs-12 col-md-12" style="padding:0px;">
					        <div id="grid_history"></div>
					    <div>
					    <script id="toolbar_template_history" type="text/x-kendo-template">
					    	<div>
					            <span class="pull-left">
					                <ul class="fieldlist" style="float:left;">
					                    <li>
					                        <i class="halflings-icon list"></i>
					                        <span class="title-template-grid">Lịch sử cuộc gọi</span>
					                    </li>
					                </ul>
					            </span>
					            
					            <span class="pull-right">
					                <ul class="fieldlist" style="float:left;">
					                </ul>
					            </span>
					        </div>
					    </script>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End History call -->
</form>

<script type="text/javascript">
	$(function(){
		if($('select')){
    		$("select").select2({
        		closeOnSelect: true
    		});
  		}

		$(".date-picker").kendoDatePicker({
	    	value   : '<?php echo date("Y-m-d"); ?>',
	    	format  : 'yyyy-MM-dd',
	  	});

	  	$("#id_city").change(function(){
	  		var id_city = $(this).val();
		    $.ajax({
		      url     : 'ajax/districtbycity',
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

	  		$.ajax({
		      	url     : 'ajax/statuschild1',
		      	type    : "GET",
		      	data    : {'id_call_status':id_call_status},
		      	success : function( data ) {
		        	var obj_decode = $.parseJSON(data);
		        	var district = obj_decode;
		          
		        	var html_status_c1 = '<option value="" selected="selected"> Trạng thái</option>';
		        	for(var key in district){
		          		var detail  = district[key];
		          		var id      = detail['id'];
		          		var name    = detail['name'];
		          		html_status_c1 += '<option value="' + id + '">' + name + '</option>';
		        	}
		        	$('#id_call_status_c1').html(html_status_c1);
		        	$("#id_call_status_c1").select2("val", "");
		      	},

		      	error   : function(msg){
		        	var html_status_c1 = '<option value="" selected="selected"> Trạng thái</option>';
		        	$('#id_call_status_c1').html(html_status_c1);
		        	$("#id_call_status_c1").select2("val", "");
		      	}
		    });
	  	});

	  	$("#id_call_status_c1").change(function(){
	  		var id_call_status 		= $("#id_call_status").val();
	  		var id_call_status_c1 	= $(this).val();

	  		$.ajax({
		      	url     : 'ajax/statuschild2',
		      	type    : "GET",
		      	data    : { 'id_call_status':id_call_status, 'id_call_status_c1':id_call_status_c1 },
		      	success : function( data ) {
		        	var obj_decode = $.parseJSON(data);
		        	var district = obj_decode;
		          
		        	var html_status_c2 = '<option value="" selected="selected"> Trạng thái</option>';
		        	for(var key in district){
		          		var detail  = district[key];
		          		var id      = detail['id'];
		          		var name    = detail['name'];
		          		html_status_c2 += '<option value="' + id + '">' + name + '</option>';
		        	}
		        	$('#id_call_status_c2').html(html_status_c2);
		        	$("#id_call_status_c2").select2("val", "");
		      	},

		      	error   : function(msg){
		        	var html_status_c2 = '<option value="" selected="selected"> Trạng thái</option>';
		        	$('#id_call_status_c2').html(html_status_c2);
		        	$("#id_call_status_c2").select2("val", "");
		      	}
		    });
	  	});
		
		$("#id_group").change(function(){
			var id_group = $(this).val();

			$.ajax({
		      	url     : 'ajax/agentbygroup',
		      	type    : "GET",
		      	data    : { 'id_group':id_group },
		      	success : function( data ) {
		        	var obj_decode = $.parseJSON(data);
		        	var agent = obj_decode;
		          
		        	var html_agent = '<option value="" selected="selected"> Chọn nhân viên</option>';
		        	for(var key in agent){
		          		var detail  = agent[key];
		          		var id      = detail['id'];
		          		var full_name    = detail['full_name'];
		          		var username    = detail['username'];
		          		html_agent += '<option value="' + id + '">' + full_name + ' (' + username + ')</option>';
		        	}
		        	$('#id_agent').html(html_agent);
		        	$("#id_agent").select2("val", "");
		      	},

		      	error   : function(msg){
		        	var html_agent = '<option value="" selected="selected"> Chọn nhân viên</option>';
		        	$('#id_agent').html(html_agent);
		        	$("#id_agent").select2("val", "");
		      	}
		    });
		});

		$("#id_center_city").change(function(){
			var id_city = $(this).val();

			$.ajax({
		      	url     : 'ajax/centerbycity',
		      	type    : "GET",
		      	data    : { 'id_city':id_city },
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

		$("#id_center").change(function(){
			var id_center = $(this).val();

			$.ajax({
		      	url     : 'ajax/frametimebycenter',
		      	type    : "GET",
		      	data    : { 'id_center':id_center },
		      	success : function( data ) {
		        	var obj_decode = $.parseJSON(data);
		        	var center = obj_decode;
		          
		        	var html_frametime = '<option value="" selected="selected"> Chọn ca hẹn</option>';
		        	for(var key in center){
		          		var detail  = center[key];
		          		var id      = detail['id'];
		          		var name    = detail['name'];
		          		var start   = detail['start'];
		          		var end    	= detail['end'];
		          		html_frametime += '<option value="' + id + '"> ' + name + ' (' + start + '-' + end + ')</option>';
		        	}
		        	$('#id_frametime').html(html_frametime);
		        	$("#id_frametime").select2("val", "");
		      	},

		      	error   : function(msg){
		        	var html_frametime = '<option value="" selected="selected"> Chọn ca hẹn</option>';
		        	$('#id_frametime').html(html_frametime);
		        	$("#id_frametime").select2("val", "");
		      	}
		    });
		});
	});
</script>