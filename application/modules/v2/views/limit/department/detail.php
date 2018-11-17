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

    .txt_limit{
      width: 26px;
      float: left;
    }

    .col_hd_limit{
      width: 65px;
    }

    .col_bd_limit{
      
    }

    .label_limit{
      margin-left: 15px;
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
      <label class="control-label col-sm-2 dialog-control" for="pwd">Từ ngày </label>
      <div class="col-sm-10 dialog-control">
        <input type="text" id="startdate_dialog" name="startdate" class="form-control date-picker" placeholder="Chọn ngày" value="<?php echo date('Y-m-d');?>">
        <span class="error"><?php echo form_error('startdate'); ?></span>
      </div>
    </div>

    <div class="form-group">
      <label class="control-label col-sm-2 dialog-control" for="pwd">Trung tâm Spa </label>
      <div class="col-sm-10 dialog-control">
        <select id="id_center_spa_dialog" name="id_center_spa" class="form-control-select">
          <option value="0" > Chọn Trung tâm Spa</option>
          <?php
          if( isset($center_spa) AND !empty($center_spa) ){
            foreach ($center_spa as $key => $value) {
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
      <label class="control-label col-sm-2 dialog-control" for="pwd">Trung tâm Call </label>
      <div class="col-sm-10 dialog-control">
        <select id="id_center_dialog" name="id_center" class="form-control-select">
          <option value="0" > Chọn Trung tâm Call</option>
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
      <label class="control-label col-sm-2 dialog-control" for="pwd">Giơi hạn </label>
      <div class="col-sm-10 dialog-control">
        <table class="table table-bordered" id="limit_array">
          
        </table>
      </div>
    </div>

  	<div class="form-group">
    	<div class="col-sm-offset-2 col-sm-10 dialog-control">
        	<button type="submit" name="form_action_submit" class="btn btn-primary" style="height: 26px;">Lưu thông tin</button>
    	</div>
  	</div>
</form>

<script type="text/javascript">
  var frametime;
  $(function(){
    frametime = $.parseJSON('<?php echo $frametime; ?>');

    $(".setnumber").keypress(function(e){
      console.log(e);
        /*if(e.keyCode == 13){
            onLoadGrid();
        }*/
    });
  });

	$('#id_center_dialog').select2({
    closeOnSelect: true
  });

  $('#id_center_spa_dialog').select2({
    closeOnSelect: true
  });

  $('#id_department_dialog').select2({
    closeOnSelect: true
  });

  $("#startdate_dialog").kendoDatePicker({
    format  : 'yyyy-MM-dd',
  });

  $("#enddate_dialog").kendoDatePicker({
    format  : 'yyyy-MM-dd',
  });

  function isNumberKey(evt){
     var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 32 && (charCode < 48 || charCode > 57)||(charCode==32)){
      return false;
    }
    return true;
  } 

  $('#id_center_dialog').change(function(){
    var id_center_spa  = $('#id_center_spa_dialog').val();
    if(id_center_spa == '' || parseInt(id_center_spa) < 1){
      alert('Bạn chưa chọn Trung tâm Spa');
      $("#id_center_dialog").select2("val", "0");
      $('#limit_array').append('');
      return false;
    }
    var id_center_call = $(this).val();
    if(id_center_call !== '' && parseInt(id_center_call) > 0 ){
      // view grid
      $.ajax({
        url     : '<?php echo base_url();?>ajax/departmentbycenter',
        type    : "GET",
        data    : {'id_center':id_center_call},
        success : function( data ) {
          var obj_decode = $.parseJSON(data);
          var department = obj_decode;
          var col = department.length + 3;

          var thead = '<thead>';
          thead += '<tr>';
          thead += '<th>Ca</th>';
          thead += '<th>Spa</th>';
          thead += '<th>Call</th>';
          for(var key in department){
            var detail  = department[key];
            var id      = detail['id'];
            var name    = detail['name'];
            thead += '<th class="col_hd_limit">'+name+'</th>';
          }
          thead += '</tr>';
          thead += '</thead>';

          var tbody = '<tbody>';
          for(var key in frametime){
            var detail = frametime[key];
            var id    = detail['id'];
            var name  = detail['name'];

            tbody += '<input type="hidden" name="id_frametime[]" value="'+id+'">';
            tbody += '<tr>';
            tbody += '<td>'+name+'</td>';
            tbody += '<td><label id="cen_spa_'+id+'" name="limit_cen_frame[]" class="control-label col-sm-2 dialog-control" style="margin-left:15px;"></label></td>';
            tbody += '<td><label id="cen_call_'+id+'" name="limit_dep_frame[]" class="control-label col-sm-2 dialog-control" style="margin-left:15px;"></label></td>';
            for(var _key in department){
              var _detail  = department[_key];
              var _id      = _detail['id'];
              var _name    = _detail['name'];
              tbody += '<td class="col_bd_limit">';
              tbody += '<input type="text" id="limit_'+id+'_'+_id+'" name="limit_'+id+'_'+_id+'" value="" class="txt_limit" onkeypress="return isNumberKey(event);">';
              tbody += '<input type="text" id="limited_'+id+'_'+_id+'" name="limited_'+id+'_'+_id+'" value="" class="txt_limit" readonly>';
              tbody += '</td>';
            }
            tbody += '</tr>';
          }
          tbody += '</tbody>';
          $('#limit_array').append(thead + tbody);

          // limit
          var date = $('#startdate_dialog').val();
          $.ajax({
            url     : '<?php echo base_url();?>ajax2/limitassigndep',
            type    : "GET",
            data    : {'id_center_call':id_center_call, 'id_center_spa':id_center_spa, 'date':date},
            success : function( data ) {
              var obj_decode = $.parseJSON(data);
              var call = obj_decode['call'];
              var spa = obj_decode['spa'];

              for(var key in call){
                var detail = call[key];
                var id_frametime = detail['id_frametime'];
                var limit = detail['limit'];
                var schedule = detail['schedule'];
                $('#cen_call_'+id_frametime).html(limit+'-'+schedule);
              }

              for(var key in spa){
                var detail = spa[key];
                var id_frametime = detail['id_frametime'];
                var limit = detail['limit'];
                var schedule = detail['schedule'];
                $('#cen_spa_'+id_frametime).html(limit+'-'+schedule);
              }
            },
            error   : function(msg){
              
            }
          });

          $.ajax({
            url     : '<?php echo base_url();?>ajax2/limitedassigndep',
            type    : "GET",
            data    : {'id_center_call':id_center_call, 'id_center_spa':id_center_spa, 'date':date},
            success : function( data ) {
              var obj_decode = $.parseJSON(data);

              for(var key in obj_decode){
                var detail = obj_decode[key];
                var id_department = detail['id_department'];
                var id_frametime = detail['id_frametime'];
                var limit = detail['limit'];
                var schedule = detail['schedule'];
                $('#limit_'+id_frametime+'_'+id_department).val(limit);
                $('#limited_'+id_frametime+'_'+id_department).val(schedule);
              }
            },
            error   : function(msg){
              
            }
          });
        },
        error   : function(msg){
          $('#limit_array').append('');
        }
      });
    }else{
      $('#limit_array').html('');
    }
  });

  $('#id_center_spa_dialog').change(function(){
    $("#id_center_dialog").select2("val", "0");
    $('#limit_array').html('');
  });

  $("#startdate_dialog").change(function(){
    $('#id_center_spa_dialog').select2("val", "0");
    $("#id_center_dialog").select2("val", "0");
    $('#limit_array').html('');
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