<!DOCTYPE html>
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

    	.table-bordered{
    		border-right: 0px;
    	}

    	.table > tbody > tr > td,
    	.table > tbody > tr > th,
    	.table > tfoot > tr > td,
    	.table > tfoot > tr > th{
    		text-align: center;
    		padding: 8px 1px;
			font-size: 24px;
    	}

    	.table > tbody > tr > td:first-child,
    	.table > tbody > tr > th:first-child,
    	.table > tfoot > tr > td:first-child,
    	.table > tfoot > tr > th:first-child{
    		text-align: left;
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
	        		<table class="table">
	          			<?php
	          			$maxrow = 12;
	          			echo '<tr style="vertical-align: top;">';
						$count = count($department);
						$width = intval(100/$count);
						foreach ($department as $key => $value) {
							$i=0;
							$total1 = 0;
							$total2 = 0;

							$id_department = $value['id'];
							$name_department = $value['name'];
							echo '<td style="width: '.$width.'%;">';
							echo '<table class="table table-bordered table-hover">';
							echo '<tr>';
								echo '<th>Team</th>';
							    echo '<th>T</th>';
							    echo '<th>+1</th>';
						  	echo '</tr>';
							foreach ($group as $_key => $_value) {
								$id_group = $_value['id'];
								$name_group = $_value['name'];
								$_id_department = $_value['id_department'];
								if($_id_department == $id_department){
									unset($group[$_key]);
									$i++;

									$val1 = 0;
									if(isset($dataapp[$id_department.'-'.$id_group.'-'.$datenow])){
										$val1 = $dataapp[$id_department.'-'.$id_group.'-'.$datenow];
									}
									$total1 += $val1;

									$val2 = 0;
									if(isset($dataapp[$id_department.'-'.$id_group.'-'.$datenext])){
										$val2 = $dataapp[$id_department.'-'.$id_group.'-'.$datenext];
									}
									$total2 += $val2;

									echo '<tr>';
									echo '<td>&nbsp;'.$name_group.'</td>';
									echo '<td style="width: 45px;">'.$val1.'</td>';
									echo '<td style="width: 45px;">'.$val2.'</td>';
									echo '</tr>';
								}
							}

							if($i + 1 == $maxrow){
								echo '<tr>';
								echo '<td>&nbsp;Tổng</td>';
								echo '<td style="width: 45px;">'.$total1.'</td>';
								echo '<td style="width: 45px;">'.$total2.'</td>';
								echo '</tr>';
							}else{
								if($i < $maxrow){
									for ($j=($i+1); $j <= $maxrow; $j++) { 
										if($j != $maxrow){
											echo '<tr>';
											echo '<td>&nbsp;</td>';
											echo '<td style="width: 45px;"></td>';
											echo '<td style="width: 45px;"></td>';
											echo '</tr>';
										}else{
											echo '<tr>';
											echo '<td>&nbsp;Tổng</td>';
											echo '<td style="width: 45px;">'.$total1.'</td>';
											echo '<td style="width: 45px;">'.$total2.'</td>';
											echo '</tr>';
										}
									}
								}
							}
							echo '</table>';
							echo '</td>';
						}
						echo '</tr>';?>
		        	</table>
		      	</div>
		    </div>
	  	</div>
	</div>
	<script type="text/javascript">
		function load_content(){
		    $.ajax({
		      	url     : '<?php echo base_url();?>showapp/loadcontent',
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