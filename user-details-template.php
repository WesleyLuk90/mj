<?php
/*
	Template Name:User Details
*/
	$iseditor = current_user_can('mj_edit_games');
	$userid = (int)stripslashes_deep($_GET['id']);
	$userinfo = get_userdata($userid);
	$username = $userinfo->display_name;
	$games = mj_get_games_of_player($userid);

	get_header();
	echo "<h3>User Details for $username</h3>";
	?>
	<div class="row-fluid">
		<div class="span4">
			<table class="table">
				<tr>
					<th>Games Played</th>
					<td><?php echo count($games) ?></td>
				</tr>
			</table>
		</div>
	</div>
	<h4>VS Placement Table</h4>
	<?php
		$placements = array_map('mj_get_game_placement', $games);
		$tallies = mj_tally_player_placement($userid, $placements);
		$playernames = mj_get_players_names(mj_get_player_ids_from_games($games));
	?>
	<table class="table">
		<thead>
			<tr><th>Player</th><th>Firsts</th><th>Seconds</th><th>Thirds</th><th>Forths</th><th>Games Played</th></tr>
		</thead>
		<tbody>
			<?php
			foreach ($tallies as $playerid => $tally) {
				echo "<tr>";
				echo "<td>{$playernames[$playerid]}</td>";
				echo "<td>" . mj_format_percent($tally['first'] / $tally['count']) . "</td>";
				echo "<td>" . mj_format_percent($tally['second'] / $tally['count']) . "</td>";
				echo "<td>" . mj_format_percent($tally['third'] / $tally['count']) . "</td>";
				echo "<td>" . mj_format_percent($tally['forth'] / $tally['count']) . "</td>";
				echo "<td>{$tally['count']}</td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
	<h4>Games</h4>
	<?php
	get_template_part('mj', 'gametable');
	get_footer();
?>