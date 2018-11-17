﻿<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico"/>
	<title>CCSI - MONITOR</title>

	<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/bootstrap/css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/bootstrap/css/font-awesome.min.css"/>

    <script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery-1.12.3.min.js"></script>
    <style type="text/css">
    	.headcap{
    		font-weight: bold;
    		font-size: 38px;
			color: blue;
    	}

    	.valapp{
    		font-weight: bold;
    	}

    	.table > thead > tr > td,
    	.table > thead > tr > th{
    		text-align: center;
    		padding: 8px 8px;
    		font-size: 36px;
    	}

    	.table > tbody > tr > td,
    	.table > tbody > tr > th,
    	.table > tfoot > tr > td,
    	.table > tfoot > tr > th{
    		text-align: center;
    		padding: 6px 8px;
			font-size: 24px;
    	}

    	.table > tbody > tr > td:first-child,
    	.table > tbody > tr > th:first-child,
    	.table > tfoot > tr > td:first-child,
    	.table > tfoot > tr > th:first-child{
    		text-align: center;
    		vertical-align: middle;
    		padding: 8px 8px;
    		font-size: 42px;
    	}

    	.low{
    		background-color: rgb(40, 40, 40);
    		color: #FFF;
    	}

    	.medium{
    		background-color: rgb(255, 255, 0);
    	}

    	.high{
    		background-color: rgb(0, 255, 0);
    	}

    	.over{
    		background-color: rgb(255, 0, 0);
    		color: #FFF;
    	}

    	.totalapp{
    		font-size: 48px;
    	}

		.limited{
			font-size: 24px;
		}
    </style>
</head>
<body>
	<div class="container-fluid">
	  	<div class="row">
	    	<div class="col-xs-12">
	      		<div id="appload" class="table-responsive">
	        		<table summary="This table shows how to create responsive tables using Bootstrap's default functionality" class="table table-bordered table-hover">
	          			<thead>
	            			<tr>
	              				<th width="165px">Ca</th>
	              				<?php
	              				if(isset($center) AND !empty($center)){
	              					foreach ($center as $key => $value) {
	              						echo '<th>'.$value['name'].'</th>';
	              					}
	              				}
	              				?>
	            			</tr>
	          			</thead>
	          			<tbody>
	          				<?php
	          				$totalapp = array();
          					foreach ($framenow as $key => $value) {
          						$id_frame = $value['id'];
          						$name_frame = $value['name'];
          						$start_frame = $value['start'];
          						$end_frame = $value['end'];
          						echo '<tr>';
          						echo '<td><b>'.$name_frame.'</b></td>';
          						
          						foreach ($center as $_key => $_value) {
          							$id_center = $_value['id'];
          							$code_center = strtolower($_value['code']);
          							$name_center = $_value['name'];
          							$showup_center = $_value['showup'];

          							$target = $showup_center;
									$income = 0;
									$round = 0;

									if( isset($dataResult[$id_center.'_'.$id_frame]) AND !empty($dataResult[$id_center.'_'.$id_frame]) ){
										$income = $dataResult[$id_center.'_'.$id_frame];
										if($target > 0){
							    			$round = round( ($income*100)/$target, 0, PHP_ROUND_HALF_DOWN);
							    		}
									}

									if( isset($totalapp[$id_center]) )
									{
										$totalapp[$id_center] = $totalapp[$id_center] + $income;
									}else{
										$totalapp[$id_center] = $income;
									}

									if($round < 61){
										echo '<td class="valapp low"><strong class="totalapp">'.$income.' </strong><strong class="limited">'.$target.'</strong></td>';
									}elseif($round < 86){
										echo '<td class="valapp medium"><strong class="totalapp">'.$income.' </strong><strong class="limited">'.$target.'</strong></td>';
									}elseif($round < 101){
										echo '<td class="valapp high"><strong class="totalapp">'.$income.' </strong><strong class="limited">'.$target.'</strong></td>';
									}else{
										echo '<td class="valapp over"><strong class="totalapp">'.$income.' </strong><strong class="limited">'.$target.'</strong></td>';
									}
          						}
          						echo '</tr>';
          					}
	          				?>
	          			</tbody>
		          		<tfoot>
		            		<tr>
		            			<th>TỔNG</th>
		              			<?php
		              			foreach ($totalapp as $key => $value) {
		              				echo '<th><strong class="totalapp">'.$value.' </strong></th>';
		              			}
		              			?>
		            		</tr>
		          		</tfoot>
		        	</table>
		      	</div>
		    </div>
	  	</div>
	</div>
	<script type="text/javascript">
		function load_content(){
		    $.ajax({
		      	url     : '<?php echo base_url();?>showup/loadcontent',
		      	dataType: 'html',
		      	type    : 'GET',
		      	data    : {},
		      	success : function(response, status, jqXHR){
		          	$('#appload').html(response);
		      	},
		      	error   : function(jqXHR, status, err){
		      	},
		      	complete: function(jqXHR, status){
		      	}
		    });
	  	}
	  	$( document ).ready(function() {
		    setInterval(function(){load_content()},1000 * 60);
	  	});
	</script>
</body>
</html>