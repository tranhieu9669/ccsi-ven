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
    	.table > tbody > tr > td,
    	.table > tbody > tr > th,
    	.table > tfoot > tr > td,
    	.table > tfoot > tr > th,
    	.table > thead > tr > td,
    	.table > thead > tr > th{
    		vertical-align: middle;
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

		.table > tbody > tr > td.align-left{
			text-align: left;
			margin-left: 15px;
		}
    </style>
</head>
<body>
	<div class="container-fluid">
	  	<div id='content-load'>
		    <table class="table table-bordered">
		        <thead>
		          	<tr>
		            	<th width="50px">STT</th>
			        	<th>Tên Nhận viên</th>
			            <th width="95px">Nhóm</th>
			        	<th width="95px">Demo</th>
			        	<th width="95px">Sale</th>
			            <th width="95px">D/S</th>
			            <th width="95px">Return</th>
			            <th width="95px">R/D</th>
		          	</tr>
		        </thead>
		        <tbody>
		        	<?php
		        		if(isset($data) AND !empty($data)){
		        			$i = 1;
		        			foreach ($data as $key => $value) {
		        				$fullname = $value['fullname'];
		        				$demo = $value['demo'];
		        				$sale = $value['sale'];
		                        $return = $value['rtn'];
		                        $team = $value['team'];
		                        $ds = 0;
		                        if($demo > 0){
		                            $ds = round( ($sale*100)/$demo, 2, PHP_ROUND_HALF_DOWN);
		                        }
		                        $rd = 0;
		                        if($demo > 0){
		                            $rd = round( ($return*100)/$demo, 2, PHP_ROUND_HALF_DOWN);
		                        }
		        				echo '<tr>';
		        					echo '<td>'.$i.'</td>';
		        					echo '<td class="align-left">'.$fullname.'</td>';
		        					echo '<td>'.$team.'</td>';
		    			        	echo '<td>'.$demo.'</td>';
		    			        	echo '<td>'.$sale.'</td>';
		                            echo '<td>'.$ds.'%</td>';
		                            echo '<td>'.$return.'</td>';
		                            echo '<td>'.$rd.'</td>';
		        				echo '</tr>';
		        				$i++;
		        			}
		        		}
		        	?>
		        </tbody>
		    </table>
		</div>
	</div>
	<script type="text/javascript">
		function load_content(){
		    $.ajax({
		      	url     : '<?php echo base_url();?>sale/loadcontent',
		      	dataType: 'html',
		      	type    : 'GET',
		      	data    : {},
		      	success : function(response, status, jqXHR){
		          	$('#content-load').html(response);
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