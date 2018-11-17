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

  	.tab-content{
  		min-height: 350px;
  	}

  	.btn{
	    padding: 6px 12px;
  	}

  	#formSubmitShowUp .form-group:first-child{
  		padding-top: 10px;
  	}

  	.alertMessage{
  		display: none;
  	}
</style>

<div class="container-fluid">
	<div class="row-fluid">
		<div class="row12">
			<div class="widget-box">
	          	<div class="widget-title">
	          		<ul class="nav nav-tabs">
	          			<li class="active">
	          				<a data-toggle="tab" href="#tabShowup">Cấu hình ShowUp</a>
	          			</li>

	          			<li class="">
	          				<a data-toggle="tab" href="#tab">Cấu hình ABC</a>
	          			</li>
      				</ul>
	          	</div>
	          	<div class="widget-content tab-content">
	          		<div id="tabShowup" class="tab-pane active">
	          			<form id="formSubmitShowUp" class="form-horizontal" role="form" method="POST" action="<?php echo $_SERVER['REQUEST_URI'];?>" autocomplete="off">
	          				<div class="form-group" class="alertMessage">
						    	<label class="control-label col-sm-3 dialog-control" for=""></label>
						    	<div class="col-sm-9 dialog-control" id="showMessage"></div>
						  	</div>

	          				<?php
	          				$i = 0;
	          				if(isset($centerList) AND !empty($centerList)){
	          					foreach ($centerList as $key => $value) {
	          						if($i%2 == 0){
	          							if($i > 0){
	          								echo '</div>';
	          							}
	          							echo '<div class="form-group">';
	          						}
	          						###########
	          						echo '<label class="control-label col-sm-3 dialog-control" for="pwd"> '.$value['name'].'</label>';
							    	echo '<div class="col-sm-3 dialog-control">';
							      		echo '<input type="number" name="centerShowup_'.$value['id'].'" class="form-control" min="1" max="20" step="1" value="'.$value['showup'].'">';
							    	echo '</div>';
	          						###########
	          						$i++;
	          					}
	          					if($i > 0){
	          						echo '</div>';
	          					}
	          				}
	          				?>
							<div class="form-group">
						    	<label class="control-label col-sm-3 dialog-control" for="pwd"> </label>
						    	<div class="col-sm-9 dialog-control">
						      		<button type="submit" name="submit_save_data" class="btn btn btn-success dial" value="">
										<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Save
									</button>
						    	</div>
						  	</div>
						</form>
	          		</div>

	          		<div id="tab" class="tab-pane">
	          			Chưa có nội dung
	          		</div>
	      		</div>
	        </div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$('form#formSubmitShowUp').on('submit', function (e) {
    	var _data   = $(this).serializeArray();
    	var _action = $(this).attr("action");
    	var _method = $(this).attr("method").toUpperCase();
	    $.ajax({
	        url     : _action,
	        dataType: 'html',
	        type    : _method,
	        data    : _data,  
	        success : function(response, status, jqXHR){
	        	var _showMessage = '<div class="alert alert-success" role="alert">';
				_showMessage = _showMessage + '<strong>Success</strong> Cập nhật thành công.';
				_showMessage = _showMessage + '</div>';
	            $('#showMessage').html(_showMessage);
	            $('.alertMessage').fadeIn();
	        },
	        error   : function(jqXHR, status, err){
	        },
	        complete: function(jqXHR, status){
	        }
	    });

	    setTimeout(function(){
			$('.alertMessage').css('display', 'none');
		}, 2000);
	    e.preventDefault();
  	});
</script>