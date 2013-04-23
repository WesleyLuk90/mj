<?php
/*
	Template Name:User Details
*/
	require_once(get_stylesheet_directory() . "/controllers/user-details-controller.php");
	$userdetails = new UserDetails();

	get_header();
	echo "<h3>User Details for {$userdetails->getUsername()}</h3>";
	?>
	<div class="row-fluid">
		<div class="span4">
			<table class="table">
				<tr>
					<th>Games Played</th>
					<td><?php echo $userdetails->getGamesPlayed() ?></td>
				</tr>
			</table>
		</div>
	</div>
	<h4>VS Placement Table</h4>
	<?php
		$tallies = $userdetails->getPlacementTally();
	?>
	<table class="table">
		<thead>
			<tr><th>Player</th><th>Firsts</th><th>Seconds</th><th>Thirds</th><th>Forths</th><th>Games Played</th></tr>
		</thead>
		<tbody>
			<?php
			foreach ($tallies as $playerid => $tally) {
				echo "<tr>";
				echo "<td>{$tally['playername']}</td>";
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
		$userdetails->printGameTable();
	get_footer();
?>