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