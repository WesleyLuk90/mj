<?php
/*
	Template Name:User Details
*/
	require_once(get_stylesheet_directory() . "/controllers/user-details-controller.php");
	$userdetails = new UserDetails();
	$averages = $userdetails->getPlayerRunningAverage();
	$currentAverage = round($averages[count($averages) - 1]['average']);

	get_header();
	echo "<h3>User Details for {$userdetails->getUsername()}</h3>";
	echo "<p>Average:$currentAverage</p>"
	?>
	<div class="row-fluid">
		<div class="span4">
			<table class="table">
				<tr>
					<th>Games Played</th>
					<td><?php echo $userdetails->getGamesPlayed() ?></td>
				</tr>
				<tr>
					<th>Firsts</th>
					<td><?php echo $userdetails->getPlayerFirsts() ?></td>
				</tr>
				<tr>
					<th>Seconds</th>
					<td><?php echo $userdetails->getPlayerSeconds() ?></td>
				</tr>
				<tr>
					<th>Thirds</th>
					<td><?php echo $userdetails->getPlayerThirds() ?></td>
				</tr>
				<tr>
					<th>Forths</th>
					<td><?php echo $userdetails->getPlayerForths() ?></td>
				</tr>
			</table>
		</div>
	</div>
	<h4>VS Placement Table</h4>
	<table class="table">
		<thead>
			<tr><th>Player</th><th>Firsts</th><th>Seconds</th><th>Thirds</th><th>Forths</th><th>Games Played</th></tr>
		</thead>
		<tbody>
			<?php
			for ($i=0; $i < $userdetails->getTallyCount(); $i++) { 
				echo "<tr>";
				echo "<td>" . $userdetails->getTallyPlayerName($i) . "</td>";
				echo "<td>" . mj_format_percent($userdetails->getTallyFirsts($i)) . "</td>";
				echo "<td>" . mj_format_percent($userdetails->getTallySeconds($i)) . "</td>";
				echo "<td>" . mj_format_percent($userdetails->getTallyThirds($i)) . "</td>";
				echo "<td>" . mj_format_percent($userdetails->getTallyFourths($i)) . "</td>";
				echo "<td>" . $userdetails->getTallyGameCount($i) . "</td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
	<div class="running-average-graph" data-games="<?php echo esc_attr(json_encode($userdetails->getPlayerRunningAverage())); ?>">
	</div>
	<h4>Games</h4>
	<?php
		$userdetails->printGameTable();
	get_footer();
?>