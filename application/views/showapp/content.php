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