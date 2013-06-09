<?php
/*
	Template Name:Game List
*/
/**
 * Insert a new game into the table
 */
$isEditor = current_user_can('mj_edit_games');
if($isEditor){
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
			'time2' => $mjdb->get_datetime2_now,
			'player_1_id' => $p1id,
			'player_2_id' => $p2id,
			'player_3_id' => $p3id,
			'player_4_id' => $p4id,
			'player_1_score' => $p1points,
			'player_2_score' => $p2points,
			'player_3_score' => $p3points,
			'player_4_score' => $p4points
		), array(
			'%d',
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
mj_print_game_table(array(
	'isEditor' => $isEditor,
	'games' => $games,
));
if($isEditor){ ?>
	<form method="POST" class="form-horizontal">
		<fieldset>
			<legend>Create Game</legend>
		<?php
			wp_nonce_field( 'create-game');

			mj_print_player_select(array('label'=>'Player 1', 'name' => 'p1-userid'));
			mj_print_point_input_box(array('name' => 'p1-points', 'class' => 'input-mini point-input'));
			mj_print_player_select(array('label'=>'Player 2', 'name' => 'p2-userid'));
			mj_print_point_input_box(array('name' => 'p2-points', 'class' => 'input-mini point-input'));
			mj_print_player_select(array('label'=>'Player 3', 'name' => 'p3-userid'));
			mj_print_point_input_box(array('name' => 'p3-points', 'class' => 'input-mini point-input'));
			mj_print_player_select(array('label'=>'Player 4', 'name' => 'p4-userid'));
			mj_print_point_input_box(array('name' => 'p4-points', 'class' => 'input-mini point-input'));
		?>
		<div class="control-group">
			<div class="controls"><p>Total:<span class="total">0</span></p></div>
		</div>
		<?php
			add_action('wp_print_footer_scripts', 'print_on_page_script');
			function print_on_page_script(){
				?>
				<script type="text/javascript">
					$(function(){
						$('.point-input').on('change', function(){
							var inputs = $('.point-input');
							var total = 0;
							for (var i = 0; i < inputs.length; i++) {
								total += parseFloat($(inputs[i]).val()) || 0;
							};
							$('.total').text(Math.round(total * 10) / 10);
						});
					});
				</script>
				<?php
			}
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