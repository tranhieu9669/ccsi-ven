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
      		echo '<label class="control-label col-sm-2 dialog-control"></label>';
      		echo '<div class="col-sm-10 dialog-control">';
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
      		echo '<label class="control-label col-sm-2 dialog-control"></label>';
      		echo '<div class="col-sm-10 dialog-control">';
        		echo '<div class="alert alert-warning" role="alert">';
          			echo '<strong>Warning</strong> ' . $error;
        		echo '</div>';
      		echo '</div>';
    	echo '</div>';
  	}
  	?>
    <div class="form-group">
      <label class="control-label col-sm-2 dialog-control" for="pwd">Trung tâm </label>
      <div class="col-sm-10 dialog-control">
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
      <label class="control-label col-sm-2 dialog-control" for="pwd">Phòng ban </label>
      <div class="col-sm-10 dialog-control">
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
    	<label class="control-label col-sm-2 dialog-control" for="pwd">Tên Nhóm </label>
    	<div class="col-sm-10 dialog-control">
      	<input type="text" name="name" class="form-control" placeholder="Tên Nhóm" value="<?php echo set_value_input('name', $detail);?>">
      	<span class="error"><?php echo form_error('name'); ?></span>
    	</div>
  	</div>

    <div class="form-group">
      <label class="control-label col-sm-2 dialog-control" for="pwd">Host </label>
      <div class="col-sm-4 dialog-control">
        <input type="text" name="host" class="form-control" placeholder="Host" value="<?php echo set_value_input('host', $detail);?>"/>
        <span class="error"><?php echo form_error('host'); ?></span>
      </div>

      <label class="control-label col-sm-2 dialog-control" for="pwd">DB Name </label>
      <div class="col-sm-4 dialog-control">
          <input type="text" name="dbname" class="form-control" placeholder="DB Name" value="<?php echo set_value_input('dbname', $detail);?>"/>
          <span class="error"><?php echo form_error('dbname'); ?></span>
      </div>
    </div>

    <div class="form-group">
      <label class="control-label col-sm-2 dialog-control" for="pwd">Username </label>
      <div class="col-sm-4 dialog-control">
        <input type="text" name="user" class="form-control" placeholder="Username" value="<?php echo set_value_input('user', $detail);?>"/>
        <span class="error"><?php echo form_error('user'); ?></span>
      </div>

      <label class="control-label col-sm-2 dialog-control" for="pwd">Password </label>
      <div class="col-sm-4 dialog-control">
          <input type="text" name="pass" class="form-control" placeholder="Password" value="<?php echo set_value_input('pass', $detail);?>"/>
          <span class="error"><?php echo form_error('pass'); ?></span>
      </div>
    </div>

  	<div class="form-group">
    	<div class="col-sm-offset-2 col-sm-10 dialog-control">
        	<button type="submit" name="form_action_submit" class="btn btn-primary" style="height: 26px;">Lưu thông tin</button>
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

  $('#status').select2({
    closeOnSelect: true
  });

  $('#id_center').change(function(){
    var id_center = $(this).val();
    $.ajax({
      url     : '<?php echo base_url();?>admin/ajax/debyce',
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
      },

      error   : function(msg){
        var html_department = '<option value="" selected="selected"> Chọn Phòng</option>';
        $('#id_department').html(html_department);
        $("#id_department").select2("val", "");
      }
    });
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