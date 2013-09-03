<?php
	global $isEditor, $gameData;
?>
<form method="POST">
<?php wp_nonce_field( 'delete-game'); ?>
<table class="table">
	<thead>
		<tr><th>Date</th><th>Player 1</th><th>Player 2</th><th>Player 3</th><th>Player 4</th><?php if($isEditor) echo "<th></th>"; ?></tr>
	</thead>
	<tbody>
		<?php

		function print_table_data($data, $i){
			echo "<td>";
			printf("<a href=\"%s\">", esc_attr(mj_get_user_detail_page_url($data['ids'][$i])));
			echo $data['names'][$i];
			echo "</a>";
			echo "<br>{$data['scores'][$i]}";
			echo "</td>";
		}

		foreach ($gameData as $key => $data) {
			echo "<tr>";
			if($data['time2'] == 0){
				echo "<td>N/A</td>";
			} else {
				echo "<td>";
				$time = new DateTime();
				$time->setTimeStamp($data['time2']);
				$time->setTimeZone(new DateTimeZone('America/Vancouver'));
				echo $time->format('Y-m-d H:i:s');
				echo "</td>";
			}
			print_table_data($data, 0);
			print_table_data($data, 1);
			print_table_data($data, 2);
			print_table_data($data, 3);
			if($isEditor){
				$attr = sprintf('type="submit" title="delete" class="btn btn-danger" name="game-id" value="%d"', $data['game_id']);
				echo '<td><button ' . $attr . '"><span class="glyphicon glyphicon-remove"></button></td>';
			}
			echo "</tr>";
		}
		?>
	</tbody>
</table>
</form>