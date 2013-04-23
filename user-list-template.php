<?php
/*
	Template Name:User List
*/
get_header();
require_once(get_stylesheet_directory() . "/controllers/user-list-controller.php");
$userlist = new UserList();
?>
<h4><?php the_title() ?></h4>
<table class="table">
	<thead>
		<tr><th>Player</th><th>Average</th></tr>
	</thead>
	<tbody>
		<?php for ($i=0; $i < $userlist->getPlayerCount(); $i++): ?>
			<tr>
				<td><a href="<?php echo mj_get_user_detail_page_url($userlist->getPlayerID($i)) ?>">
					<?php echo $userlist->getPlayerName($i); ?></a></td>
				<td><?php echo mj_format_score($userlist->getPlayerAverage($i)); ?></td>
			</tr>
		<?php endfor; ?>
	</tbody>
</table>
<?php
get_footer();
?>