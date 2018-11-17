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