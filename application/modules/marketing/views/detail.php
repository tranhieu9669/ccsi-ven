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

    .content-call{
      border: 1px solid #CCC;
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
    			echo 'refresh_grid("#grid_introduced");';
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
    	<label class="control-label col-sm-2 dialog-control" for="pwd">Tên Khách hàng </label>
    	<div class="col-sm-4 dialog-control">
    		<input type="text" id="fullname" name="fullname" class="form-control" placeholder="Tên Khách hàng" value="<?php echo set_value_input('fullname', $detail);?>">
    		<span class="error"><?php echo form_error('fullname'); ?></span>
    	</div>

      <label class="control-label col-sm-2 dialog-control" for="pwd">Số điện thoại </label>
      <div class="col-sm-4 dialog-control">
        <input type="text" name="mobile" class="form-control" placeholder="Số điện thoại" value="<?php echo set_value_input('mobile', $detail);?>">
        <span class="error"><?php echo form_error('mobile'); ?></span>
      </div>
  	</div>

	<div class="form-group">
    	<label class="control-label col-sm-2 dialog-control" for="pwd">Số tuổi </label>
    	<div class="col-sm-4 dialog-control">
    		<input type="text" name="age" class="form-control" placeholder="Số tuổi" value="<?php echo set_value_input('age', $detail);?>">
    	</div>

    	<label class="control-label col-sm-2 dialog-control" for="pwd">Giới tính </label>
  		<div class="col-sm-4 box-row">
	    	<ul class="fieldlist" style="margin:0px;">
          <li>
          	<input type="radio" name="gender" id="status_1" value="Male" class="k-radio">
          	<label class="k-radio-label" for="status_1">Nam</label>
        	</li>
         	<li style="margin-left: 25px;">
          	<input type="radio" name="gender" id="status_2" value="Female" class="k-radio" checked="checked">
          	<label class="k-radio-label" for="status_2">Nữ</label>
        	</li>
        </ul>
	    </div>
  	</div>  	

  	<div class="form-group">
    	<label class="control-label col-sm-2 dialog-control" for="pwd">Đỉa chỉ </label>
    	<div class="col-sm-10 dialog-control">
    		<input type="text" name="address" class="form-control" placeholder="Đỉa chỉ khách hàng" value="<?php echo set_value_input('address', $detail);?>">
    	</div>
  	</div>

    <div class="form-group">
      <label class="control-label col-sm-2 dialog-control" for="pwd">Ghi chú </label>
      <div class="col-sm-10 dialog-control">
        <div class="col-sm-12 box-row">
          <textarea class="control-full content-call" name="app_content"><?php echo set_value_input('app_content', $detail);?></textarea>
        </div>
        <span class="error"><?php echo form_error('app_content'); ?></span>
      </div>
    </div>

    <div class="form-group">
      <label class="control-label col-sm-2 dialog-control" for="pwd">Thành phố </label>
      <div class="col-sm-4 dialog-control">
        <select id="id_city" name="id_city" class="form-control-select">
          <option value="" selected="selected"> Chọn Thành phố</option>
          <?php
          if(isset($city) AND !empty($city)){
            foreach ($city as $key => $value) {
              if( isset($detail['id_city']) AND $detail['id_city'] == $value['id'] ){
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

      <label class="control-label col-sm-2 dialog-control" for="pwd">Chi nhánh </label>
      <div class="col-sm-4 dialog-control">
        <select id="id_dialog_center" name="id_dialog_center" class="form-control-select">
          <option value="" selected="selected"> Chọn Chi nhánh</option>
          <?php
          if(isset($center) AND !empty($center)){
            foreach ($center as $key => $value) {
              if( isset($detail['id_dialog_center']) AND $detail['id_dialog_center'] == $value['id'] ){
                echo '<option value="'.$value['id'].'" selected="selected"> '.$value['name'].'</option>';
              }else{
                echo '<option value="'.$value['id'].'"> '.$value['name'].'</option>';
              }
            }
          }
          ?>
        </select>
        <span class="error"><?php echo form_error('id_dialog_center'); ?></span>
      </div>
    </div>

    <div class="form-group">
      <label class="control-label col-sm-2 dialog-control" for="pwd">Ngày hẹn </label>
      <div class="col-sm-4 dialog-control">
          <input type="text" id="appointment" name="appointment" class="form-control date-picker" placeholder="Ngày hẹn" value="<?php echo set_value_input('appointment', $detail);?>">
          <span class="error"><?php echo form_error('appointment'); ?></span>
      </div>

      <input type="hidden" id="hd_frametime_start" name="hd_frametime_start" value="<?php echo set_value_input('hd_frametime_start', $detail);?>">
      <label class="control-label col-sm-2 dialog-control" for="pwd">Ca hẹn </label>
      <div class="col-sm-4 dialog-control">
        <select id="id_frametime" name="id_frametime" class="form-control-select">
          <option value="" selected="selected" start="00:00"> Chọn Ca</option>
          <?php
          if(isset($list_frame) AND !empty($list_frame)){
            foreach ($list_frame as $key => $value) {
              if( isset($detail['id_frametime']) AND $detail['id_frametime'] == $value['id'] ){
                echo '<option value="'.$value['id'].'" selected="selected" start="'.$value['start'].'"> '.$value['name'].' ('.$value['start'].'-'.$value['end'].')</option>';
              }else{
                echo '<option value="'.$value['id'].'" start="'.$value['start'].'"> '.$value['name'].' ('.$value['start'].'-'.$value['end'].')</option>';
              }
            }
          }
          ?>
        </select>
        <span class="error"><?php echo form_error('id_frametime'); ?></span>
      </div>
    </div>

  	<div class="form-group">
    	<div class="col-sm-offset-2 col-sm-10 dialog-control">
        	<button type="submit" name="form_action_submit" class="btn btn-primary" style="height: 26px;">Lưu thông tin</button>
    	</div>
  	</div>
</form>

<script type="text/javascript">
  $(function(){
    $('#id_frametime').change(function(){
      var _start = $('#id_frametime option:selected').attr('start');
      $('#hd_frametime_start').val(_start);
    });
  });

	$("#id_city").select2({
    closeOnSelect: true
	});

  $("#id_dialog_center").select2({
    closeOnSelect: true
  });

  $("#id_frametime").select2({
    closeOnSelect: true
  });

  $("#appointment").kendoDatePicker({
    format : 'yyyy-MM-dd',
    min : '<?php echo date('Y-m-d')?>'
  });

  $("#id_city").change(function(){
    var id_city = $(this).val();
    console.log(id_city);

    $.ajax({
      url     : '<?php echo base_url();?>staff/ajax/cespabyci',
      type    : "GET",
      data    : { 'id_city':id_city, 'type': 'spa'  },
      success : function( data ) {
        var obj_decode = $.parseJSON(data);
        var center = obj_decode;
        
        var html_center = '<option value="" selected="selected"> Chọn Chi nhánh</option>';
        for(var key in center){
            var detail  = center[key];
            var id      = detail['id'];
            var code    = detail['code'];
            var name    = detail['name'];
            html_center += '<option value="' + id + '">' + name + '</option>';
        }
        $('#id_dialog_center').html(html_center);
        $("#id_dialog_center").select2("val", "");
      },

      error   : function(msg){
        var html_center = '<option value="" selected="selected"> Chọn Chi nhánh</option>';
        $('#id_dialog_center').html(html_center);
        $("#id_dialog_center").select2("val", "");
      }
    });
    $('#id_frametime').html('<option value="" selected="selected"> Chọn Ca</option>');
    $("#id_frametime").select2("val", "");
    $('#appointment').val('');
  });

  function frametimeappointment(){
    var id_center = $('#id_dialog_center').val();
    var dateapp = $('#appointment').val();

    if(dateapp != ''){
      var _dateapp = dateapp;

      $.ajax({
            url     : '<?php echo base_url();?>marketing/frameapp',
            type    : "GET",
            data    : { 'id_center':id_center, 'dateapp':dateapp },
            success : function( data ) {
              var obj_decode = $.parseJSON(data);
              var center = obj_decode;
              
              var html_frametime = '<option value="" selected="selected" start="00:00"> Chọn Ca</option>';
              for(var key in center){
                  var detail  = center[key];
                  var id      = detail['id'];
                  var name    = detail['name'];
                  var start   = detail['start'];
                  var end     = detail['end'];
                  html_frametime += '<option value="' + id + '" start="'+start+'"> ' + name + ' (' + start + '-' + end + ')</option>';
              }
              $('#id_frametime').html(html_frametime);
              $("#id_frametime").select2("val", "");
            },

            error   : function(msg){
              var html_frametime = '<option value="" selected="selected" start="00:00"> Chọn Ca</option>';
              $('#id_frametime').html(html_frametime);
              $("#id_frametime").select2("val", "");
            }
        });
    }
  }

  $("#id_dialog_center").change(function(){
    frametimeappointment();
  });

  $("#appointment").change(function(){
    frametimeappointment();
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