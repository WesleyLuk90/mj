<?php
/*
	Template Name:Game List
*/
/**
 * Insert a new game into the table
 */
$iseditor = current_user_can('mj_edit_games');
if($iseditor){
	if ( !empty($_POST) && wp_verify_nonce($_POST['_wpnonce'],'create-game') !== false ) {
		$p1points = stripslashes_deep($_POST['p1-points']) * 1000;
		$p2points = stripslashes_deep($_POST['p2-points']) * 1000;
		$p3points = stripslashes_deep($_POST['p3-points']) * 1000;
		$p4points = stripslashes_deep($_POST['p4-points']) * 1000;
		$p1id = stripslashes_deep($_POST['p1-userid']);
		$p2id = stripslashes_deep($_POST['p2-userid']);
		$p3id = stripslashes_deep($_POST['p3-userid']);
		$p4id = stripslashes_deep($_POST['p4-userid']);
		$wpdb->insert($mjdb->game_table, array(
			'time' => $mjdb->get_datetime_now(),
			'player_1_id' => $p1id,
			'player_2_id' => $p2id,
			'player_3_id' => $p3id,
			'player_4_id' => $p4id,
			'player_1_score' => $p1points,
			'player_2_score' => $p2points,
			'player_3_score' => $p3points,
			'player_4_score' => $p4points
		), array(
			'%s',
			'%d', '%d', '%d','%d',
			'%f', '%f', '%f','%f'
		));
		wp_redirect(mj_get_current_page_url());
		exit();
	}
	if ( !empty($_POST) && wp_verify_nonce($_POST['_wpnonce'],'delete-game') !== false ) {
		$game_id = stripslashes_deep($_POST['game-id']);
		$wpdb->update($mjdb->game_table, array('flag'=>$mjdb::GAME_FLAG_DELETED), array('id'=>$game_id), array('%d'));
		wp_redirect(mj_get_current_page_url());
		exit();
	}
}
get_header();

$games = mj_get_games_list();
get_template_part('mj', 'gametable');
if($iseditor){ ?>
	<form method="POST" class="form-horizontal">
		<fieldset>
			<legend>Create Game</legend>
		<?php
			wp_nonce_field( 'create-game');

			mj_print_player_select(array('label'=>'Player 1', 'name' => 'p1-userid'));
			mj_print_point_input_box(array('name' => 'p1-points'));
			mj_print_player_select(array('label'=>'Player 2', 'name' => 'p2-userid'));
			mj_print_point_input_box(array('name' => 'p2-points'));
			mj_print_player_select(array('label'=>'Player 3', 'name' => 'p3-userid'));
			mj_print_point_input_box(array('name' => 'p3-points'));
			mj_print_player_select(array('label'=>'Player 4', 'name' => 'p4-userid'));
			mj_print_point_input_box(array('name' => 'p4-points'));
		?>
		<div class="control-group">
			<div class="controls"><button type="submit" class="btn">Create Game</button></div>
		</div>
		</fieldset>
	</form>
	<?php
}

get_footer();
?>