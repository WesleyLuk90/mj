<?php
	global $iseditor, $games;
?>
<form method="POST">
<?php wp_nonce_field( 'delete-game'); ?>
<table class="table">
	<thead>
		<tr><th>Date</th><th>Player 1</th><th>Player 2</th><th>Player 3</th><th>Player 4</th><?php if($iseditor) echo "<th></th>"; ?></tr>
	</thead>
	<tbody>
		<?php
		$player_ids = mj_get_player_ids_from_games($games);
		$player_names = mj_get_players_names($player_ids);

		function print_table_data($userid, $player_names, $score){
			echo "<td>";
			printf("<a href=\"%s\">", esc_attr(mj_get_user_detail_page_url($userid)));
			echo $player_names[$userid];
			echo "</a>";
			echo "<br>$score";
			echo "</td>";
		}

		foreach ($games as $key => $row) {
			echo "<tr>";
			if($row->time == "0000-00-00 00:00:00"){
				echo "<td>N/A</td>";
			} else {
				echo "<td>{$row->time}</td>";
			}
			print_table_data($row->player_1_id, $player_names, $row->player_1_score);
			print_table_data($row->player_2_id, $player_names, $row->player_2_score);
			print_table_data($row->player_3_id, $player_names, $row->player_3_score);
			print_table_data($row->player_4_id, $player_names, $row->player_4_score);
			if($iseditor){
				$attr = sprintf('type="submit" title="delete" class="btn btn-danger" name="game-id" value="%d"', $row->id);
				echo '<td><button ' . $attr . '"><i class="icon-remove"></button></td>';
			}
			echo "</tr>";
		}
		?>
	</tbody>
</table>
</form>