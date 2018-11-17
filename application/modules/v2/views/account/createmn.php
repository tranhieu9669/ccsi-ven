<style type="text/css">
  	.alert{
    	padding: 8px;
    	margin-bottom: 0px;
    	border-radius: 0px
  	}

  	.form-control{
    	padding: 0px 0px 0px 3px;
  	}

  	.dialog-control{
    	padding-left: 0px;
    	padding-right: 0px;
  	}

  	.form-horizontal .form-group{
    	margin-left: 0px;
    	margin-right: 0px;
  	}

  	.form-horizontal .control-label{
    	padding-right: 8px;
    	margin-left: -10px;
  	}

  	.question-box{
    	border: 1px solid #ccc;
    	min-height: 100px;
    	max-height: 175px;
    	overflow-y: scroll;
  	}

  	.checkbox{
    	float: left;
    	margin-right: 20px;
  	}

  	.question-box > .checkbox{
    	float: left;
    	clear: both;
    	margin-left: 2px;
  	}

  	form#formSubmit{
    	padding: 15px 10px;  
  	}

  	input[type="checkbox"], input[type="radio"]{
    	margin: 2px 20px 0px 5px;
  	}
</style>

<form id="formSubmit" class="form-horizontal" role="form" method="POST" action="<?php echo $_SERVER['REQUEST_URI'];?>" autocomplete="off">
  	<?php
  	if( isset($success) AND !empty($success) ){
    	echo '<div class="form-group" id="msg_alert">';
      		echo '<label class="control-label col-sm-3 dialog-control"></label>';
      		echo '<div class="col-sm-9 dialog-control">';
        		echo '<div class="alert alert-success" role="alert">';
          			echo '<strong>Success</strong> ' . $success;
        		echo '</div>';
      		echo '</div>';
    	echo '</div>';

    	echo '<script type="text/javascript">';
    		echo 'setTimeout(function(){';
    			echo 'close_modal("#dialog_infor_detail");';
    			echo 'refresh_grid("#grid");';
    		echo '}, 2000)';
    	echo '</script>';
  	}

  	if( isset($error) AND !empty($error) ){
    	echo '<div class="form-group">';
      		echo '<label class="control-label col-sm-3 dialog-control"></label>';
      		echo '<div class="col-sm-9 dialog-control">';
        		echo '<div class="alert alert-warning" role="alert">';
          			echo '<strong>Warning</strong> ' . $error;
        		echo '</div>';
      		echo '</div>';
    	echo '</div>';
  	}
  	?>
    <div class="form-group">
      <label class="control-label col-sm-3 dialog-control" for="pwd">Trung tâm </label>
      <div class="col-sm-9 dialog-control">
        <select id="id_center" name="id_center" class="form-control-select">
          <option value="" > Chọn Trung tâm</option>
          <?php
          if( isset($center) AND !empty($center) ){
            foreach ($center as $key => $value) {
              if( isset($detail['id_center']) AND $detail['id_center'] == $value['id'] ){
                echo '<option value="' . $value['id'] . '" selected="selected" > ' . $value['name'] . '</option>';
              }else{
                echo '<option value="' . $value['id'] . '" > ' . $value['name'] . '</option>';
              }
            }
          }
          ?>
        </select>
        <span class="error"><?php echo form_error('id_center'); ?></span>
      </div>
    </div>

    <div class="form-group">
      <label class="control-label col-sm-3 dialog-control" for="pwd">Phòng ban </label>
      <div class="col-sm-9 dialog-control">
        <select id="id_department" name="id_department" class="form-control-select">
          <option value="" > Chọn Phòng</option>
          <?php
          if( isset($department) AND !empty($department) ){
            foreach ($department as $key => $value) {
              if( isset($detail['id_department']) AND $detail['id_department'] == $value['id'] ){
                echo '<option value="' . $value['id'] . '" selected="selected" > ' . $value['name'] . '</option>';
              }else{
                echo '<option value="' . $value['id'] . '" > ' . $value['name'] . '</option>';
              }
            }
          }
          ?>
        </select>
        <span class="error"><?php echo form_error('id_department'); ?></span>
      </div>
    </div>

    <div class="form-group">
      <label class="control-label col-sm-3 dialog-control" for="pwd">Nhóm </label>
      <div class="col-sm-9 dialog-control">
        <select id="id_group" name="id_group" class="form-control-select">
          <option value="" > Chọn Nhóm</option>
          <?php
          if( isset($group) AND !empty($group) ){
            foreach ($group as $key => $value) {
              if( isset($detail['id_group']) AND $detail['id_group'] == $value['id'] ){
                echo '<option value="' . $value['id'] . '" selected="selected" > ' . $value['name'] . '</option>';
              }else{
                echo '<option value="' . $value['id'] . '" > ' . $value['name'] . '</option>';
              }
            }
          }
          ?>
        </select>
        <span class="error"><?php echo form_error('id_group'); ?></span>
      </div>
    </div>

    <div class="form-group">
      <label class="control-label col-sm-3 dialog-control" for="pwd">Tỉnh/T.Phố </label>
      <div class="col-sm-9 dialog-control">
        <select id="id_city" name="id_city" class="form-control-select">
          <option value="" > Chọn Tỉnh/T.Phố</option>
          <?php
          if( isset($city) AND !empty($city) ){
            foreach ($city as $key => $value) {
              if( isset($detail['id_city']) AND $detail['id_city'] == $value['id'] ){
                echo '<option value="' . $value['id'] . '" selected="selected" > ' . $value['name'] . '</option>';
              }else{
                echo '<option value="' . $value['id'] . '" > ' . $value['name'] . '</option>';
              }
            }
          }
          ?>
        </select>
        <span class="error"><?php echo form_error('id_city'); ?></span>
      </div>
    </div>

  	<div class="form-group">
    	<label class="control-label col-sm-3 dialog-control" for="pwd">Tên đầy đủ </label>
    	<div class="col-sm-9 dialog-control">
      	<input type="text" name="full_name" class="form-control" placeholder="Tên Nhóm" value="<?php echo set_value_input('full_name', $detail);?>">
      	<span class="error"><?php echo form_error('full_name'); ?></span>
    	</div>
  	</div>

    <div class="form-group">
      <label class="control-label col-sm-3 dialog-control" for="pwd">Email </label>
      <div class="col-sm-9 dialog-control">
        <input type="text" name="email" class="form-control" placeholder="Email" value="<?php echo set_value_input('email', $detail);?>">
        <span class="error"><?php echo form_error('email'); ?></span>
      </div>
    </div>

    <div class="form-group">
      <label class="control-label col-sm-3 dialog-control" for="pwd">Điện thoại </label>
      <div class="col-sm-9 dialog-control">
        <input type="text" name="mobile" class="form-control" placeholder="Điện thoại" value="<?php echo set_value_input('mobile', $detail);?>">
        <span class="error"><?php echo form_error('mobile'); ?></span>
      </div>
    </div>

    <?php
    $status = 'off';
    if( isset($detail['status']) AND !empty($detail['status']) ){
      $status = $detail['status'];
    }
    ?>
  	<div class="form-group">
    	<label class="control-label col-sm-3 dialog-control" for="pwd">Trạng thái </label>
    	<div class="col-sm-9 dialog-control">
    		<select id="status" name="status" class="form-control-select">
    			<option value="on" <?php if( $status == 'on' ){ echo 'selected="selected"'; } ?>> Hoạt động</option>
    			<option value="off" <?php if( $status == 'off' ){ echo 'selected="selected"'; } ?>> Không hoạt động</option>
    		</select>
    	</div>
  	</div>

  	<div class="form-group">
      <input type="hidden" id="hdaction" name="hdaction" value="dataaction">
    	<div class="col-sm-offset-3 col-sm-9 dialog-control">
        	<button type="submit" id="dataaction" name="form_action_submit" class="btn btn-primary" style="height: 26px;">Lưu thông tin</button>
          <?php
          if( $flag ){
            echo '<button type="submit" id="resetaction" name="resetpassword" class="btn btn-primary" style="height: 26px;">Cấp lại mật khẩu</button>';
          }
          ?>
    	</div>
  	</div>
</form>

<script type="text/javascript">
	$('#id_center').select2({
    closeOnSelect: true
  });

  $('#id_department').select2({
    closeOnSelect: true
  });

  $('#id_group').select2({
    closeOnSelect: true
  });

  $('#id_city').select2({
    closeOnSelect: true
  });

  $('#status').select2({
    closeOnSelect: true
  });

  $('#id_center').change(function(){
    var id_center = $(this).val();
    $.ajax({
      url     : 'ajax/departmentbycenter',
      type    : "GET",
      data    : {'id_center':id_center},
      success : function( data ) {
        var obj_decode = $.parseJSON(data);
        var department = obj_decode;
          
        var html_department = '<option value="" selected="selected"> Chọn Phòng</option>';
        for(var key in department){
          var detail  = department[key];
          var id      = detail['id'];
          var name    = detail['name'];
          html_department += '<option value="' + id + '">' + name + '</option>';
        }
        $('#id_department').html(html_department);
        $("#id_department").select2("val", "");

        var html_group = '<option value="" selected="selected"> Chọn Nhóm</option>';
        $('#id_group').html(html_group);
        $("#id_group").select2("val", "");
      },

      error   : function(msg){
        var html_department = '<option value="" selected="selected"> Chọn Phòng</option>';
        $('#id_department').html(html_department);
        $("#id_department").select2("val", "");

        var html_group = '<option value="" selected="selected"> Chọn Nhóm</option>';
        $('#id_group').html(html_group);
        $("#id_group").select2("val", "");
      }
    });
  });

  $('#id_department').change(function(){
    var id_department = $(this).val();
    $.ajax({
      url     : 'ajax/groupbydepartment',
      type    : "GET",
      data    : {'id_department':id_department},
      success : function( data ) {
        var obj_decode = $.parseJSON(data);
        var group = obj_decode;
          
        var html_group = '<option value="" selected="selected"> Chọn Nhóm</option>';
        for(var key in group){
          var detail  = group[key];
          var id      = detail['id'];
          var name    = detail['name'];
          html_group += '<option value="' + id + '">' + name + '</option>';
        }
        $('#id_group').html(html_group);
        $("#id_group").select2("val", "");
      },

      error   : function(msg){
        var html_group = '<option value="" selected="selected"> Chọn Nhóm</option>';
        $('#id_group').html(html_group);
        $("#id_group").select2("val", "");
      }
    });
  });

  $('#dataaction').on('click', function(){
    $('#hdaction').val('dataaction');
  });

  $('#resetaction').on('click', function(){
    $('#hdaction').val('resetaction');
  });

	$('form#formSubmit').on('submit', function (e) {
    	var _data   = $(this).serializeArray();
    	var _action = $(this).attr("action");
    	var _method = $(this).attr("method").toUpperCase();
	    $.ajax({
	        url     : _action,
	        dataType: 'html',
	        type    : _method,
	        data    : _data,  
	        success : function(response, status, jqXHR){
	            $('#dialog_infor_detail').html(response);
	        },
	        error   : function(jqXHR, status, err){
	        },
	        complete: function(jqXHR, status){
	        }
	    });
	    e.preventDefault();
  	});
</script>