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

    #uploading{
        position: absolute;
        top: 0px;
        left: 0px;
        background: rgba(0,0,0,0.4);
        opacity: .4;
        z-index: 999999999;
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
        <label class="control-label col-sm-3 dialog-control" for="text1">File dữ liệu</label>
        <div class="col-sm-9 dialog-control">
            <input type="file" id="uploadedFiles" name="uploadedFiles" accept=".xls,.xlsx" style="padding-top: 7px; display:none" />
            <input type="text" id="fileSelect" name="fileSelect" class="form-control" placeholder="Chọn file" readonly>
            <span class="error"><?php echo form_error('fileSelect'); ?></span>
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
      <input type="hidden" id="hdaction" name="hdaction" value="dataaction">
    	<div class="col-sm-offset-3 col-sm-9 dialog-control">
        	<button type="submit" id="dataaction" name="form_action_submit" class="btn btn-primary" style="height: 26px;">Lưu thông tin</button>
    	</div>
  	</div>
</form>

<script type="text/javascript">
  $('#fileSelect').click(function() {
    $('#uploadedFiles').click();
  });

  $('#uploadedFiles').change(function(){
    $('#fileSelect').val($('#uploadedFiles').val());
  });

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

  $('#id_city').change(function(){
    var id_city = $(this).val();
    $.ajax({
      url     : 'ajax/centerbycity',
      type    : "GET",
      data    : {'id_city':id_city},
      success : function( data ) {
        var obj_decode = $.parseJSON(data);
        var center = obj_decode;
          
        var html_center = '<option value="" selected="selected"> Chọn Trung tâm</option>';
        for(var key in center){
          var detail  = center[key];
          var id      = detail['id'];
          var name    = detail['name'];
          html_center += '<option value="' + id + '">' + name + '</option>';
        }
        $('#id_center').html(html_center);
        $("#id_center").select2("val", "");

        var html_department = '<option value="" selected="selected"> Chọn Phòng</option>';
        $('#id_department').html(html_department);
        $("#id_department").select2("val", "");

        var html_group = '<option value="" selected="selected"> Chọn Nhóm</option>';
        $('#id_group').html(html_group);
        $("#id_group").select2("val", "");
      },

      error   : function(msg){
        var html_center = '<option value="" selected="selected"> Chọn Trung tâm</option>';
        $('#id_center').html(html_center);
        $("#id_center").select2("val", "");

        var html_department = '<option value="" selected="selected"> Chọn Phòng</option>';
        $('#id_department').html(html_department);
        $("#id_department").select2("val", "");

        var html_group = '<option value="" selected="selected"> Chọn Nhóm</option>';
        $('#id_group').html(html_group);
        $("#id_group").select2("val", "");
      }
    });
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

	$('form#formSubmit').on('submit', function (e) {
      var docHeight = $(document).height();
      var docWidth = $(document).width();
      $('html').append('<div id="uploading_acc"></div>');
      $('#uploading_acc').css('width', docWidth + 'px');
      $('#uploading_acc').css('height', docHeight + 'px');
    	//var _data   = $(this).serializeArray();
      var _data = new FormData($(this)[0]);
    	var _action = $(this).attr("action");
    	var _method = $(this).attr("method").toUpperCase();
	    $.ajax({
	        url     : _action,
	        dataType: 'html',
	        type    : _method,
	        data    : _data,
          processData: false,
          contentType: false,
	        success : function(response, status, jqXHR){
            $('#uploading_acc').fadeOut( "slow" );
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