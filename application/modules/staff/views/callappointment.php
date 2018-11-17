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
  	?>
	<!-- Customer -->
	<div class="container-fluid box-infor">
		<div class="col-sm-12">
			<div class="panel panel-primary panel-box">
				<div class="panel-heading">
					<h3 class="panel-title">THÔNG TIN LỊCH HẸN</h3>
				</div>
				<div class="panel-body">
					<div class="col-sm-6 box-item">
						<div class="col-sm-12">
							<div class="form-group">
							    <label class="control-label col-sm-4" for="pwd">Tên khách hàng </label>
							    <div class="col-sm-8 box-row">
							      	<input type="text" name="fullname" class="form-control" placeholder="Tên khách hàng" value="<?php echo set_value_input('fullname', $appointmentdt);?>" readonly>
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4" for="pwd">Số điện thoại </label>
							    <div class="col-sm-8 box-row">
							      	<input type="text" id="cus_mobile" name="cus_mobile" class="form-control" placeholder="Số điện thoại" value="<?php echo set_value_input('cus_mobile', $appointmentdt);?>" readonly>
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4" for="pwd">Trung tâm </label>
							    <div class="col-sm-8 box-row">
							      	<input type="text" name="cenname" class="form-control" placeholder="Địa chỉ" value="<?php echo set_value_input('cenname', $appointmentdt);?>" readonly>
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4" for="pwd">Ca </label>
							    <div class="col-sm-8 box-row">
							    	<input type="text" name="franame" class="form-control" placeholder="Địa chỉ" value="<?php echo set_value_input('franame', $appointmentdt);?>" readonly>
							    </div>
						  	</div>
					  	</div>
					</div>
					<div class="col-sm-6 box-item">
						<div class="col-sm-12">
							<div class="form-group">
							    <label class="control-label col-sm-4 padding-left-15" for="pwd">Agent gọi </label>
							    <div class="col-sm-8 box-row">
							    	<input type="text" name="agentname" class="form-control" placeholder="Địa chỉ" value="<?php echo set_value_input('agentname', $appointmentdt);?>" readonly>
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4 padding-left-15" for="pwd">EXT </label>
							    <div class="col-sm-8 box-row">
							      	<input type="text" name="agent_ext" class="form-control" placeholder="Địa chỉ" value="<?php echo set_value_input('agent_ext', $appointmentdt);?>" readonly>
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4 padding-left-15" for="pwd">Phòng </label>
							    <div class="col-sm-8 box-row">
							      	<input type="text" name="depname" class="form-control" placeholder="Địa chỉ" value="<?php echo set_value_input('depname', $appointmentdt);?>" readonly>
							    </div>
						  	</div>
						  	<div class="form-group">
							    <label class="control-label col-sm-4 padding-left-15" for="pwd">Nhóm </label>
							    <div class="col-sm-8 box-row">
							      	<input type="text" name="groname" class="form-control" placeholder="Địa chỉ" value="<?php echo set_value_input('groname', $appointmentdt);?>" readonly>
							    </div>
						  	</div>
					  	</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End Customer -->
	<!-- Schedule -->
	<div class="container-fluid box-infor" id="appointment_info">
		<div class="col-sm-12">
			<div class="panel panel-primary panel-box">
				<div class="panel-heading">
					<h3 class="panel-title">QUẢN LÝ LỊCH HẸN</h3>
				</div>
				<div class="panel-body">
					<div class="col-sm-12 box-item">
						<div class="form-group">
						    <label class="control-label col-sm-2" for="pwd">Giới tính <span class="validation">*</span></label>
						    <div class="col-sm-10 box-row">
						    	<ul class="fieldlist" style="margin:0px;">
						    		<li>
					                  	<input type="radio" name="app_status" id="app_status_default" value="default" class="k-radio" checked="checked">
					                  	<label class="k-radio-label" for="app_status_default">Default</label>
					              	</li>
						            <li style="margin-left: 35px;">
					                  	<input type="radio" name="app_status" id="app_status_change" value="change" class="k-radio" >
					                  	<label class="k-radio-label" for="app_status_change">Change</label>
					              	</li>
					             	<li style="margin-left: 35px;">
					                  	<input type="radio" name="app_status" id="app_status_comfirm" value="comfirm" class="k-radio" >
					                  	<label class="k-radio-label" for="app_status_comfirm">Comfirm</label>
					              	</li>
					              	<?php
					              		$app_date = '2018-01-01';
					              		if(isset($appointmentdt['app_date']) AND !empty($appointmentdt['app_date'])){
					              			$app_date = $appointmentdt['app_date'];
					              		}
					              		if( strtotime($app_date) >= strtotime(date('Y-m-d')) ){
					              			echo '<li style="margin-left: 35px;">';
					              			echo '<input type="radio" name="app_status" id="app_status_cancel" value="cancel" class="k-radio" >';
					              			echo '<label class="k-radio-label" for="app_status_cancel">Cancel</label>';
					              			echo '</li>';
					              		}
					              	?>
						        </ul>
						    </div>
					  	</div>
					</div>
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
	<div class="container-fluid box-infor">
		<button type="submit" name="submit_save_data" class="btn btn btn-success dial" value="123">
			<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Save
		</button>

		<?php 
		if(isset($appointmentdt['cus_mobile']) AND !empty($appointmentdt['cus_mobile'])){ 
			if($this->_sipname == 'deaura' OR 1){ ?>
			<a href="<?php echo base_url().'staff/dial/'.$appointmentdt['cus_mobile'];?>" class="btn btn-danger dial" target="_blank" id="dial_call">
				<span class="glyphicon glyphicon-phone-alt" aria-hidden="true"></span> Call
			</a>
			<?php }else{?>
			<a href="<?php echo 'sip:'.$appointmentdt['cus_mobile'];?>" class="btn btn-danger dial" id="dial_call">
				<span class="glyphicon glyphicon-phone-alt" aria-hidden="true"></span> Call
			</a>
			<?php }
		}
		?>
	</div>
	<!-- End Schedule -->
</form>

<script type="text/javascript">
	$(function(){
		$( "form" ).submit(function( event ) {
			var status_app = $('input[type="radio"]:checked').val();

			switch(status_app){
				case 'change':
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
					break;

				case 'comfirm':
					var content_app = $('textarea[name="content_app"]').val();
					if(content_app == ''){
						alert('Bạn chưa nhập nội dung lịch hẹn');
						return false;
					}
					break;

				case 'cancel':
					var content_app = $('textarea[name="content_app"]').val();
					if(content_app == ''){
						alert('Bạn chưa nhập nội dung lịch hẹn');
						return false;
					}
					break;

				default:
					break;
			}
			return true;
		});

		if($('select')){
    		$("select").select2({
        		closeOnSelect: true
    		});
  		}

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
		    $('#id_frametime').html('<option value="" selected="selected"> Chọn ca hẹn</option>');
		    $("#id_frametime").select2("val", "");
		    $('#appointment').val('');
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