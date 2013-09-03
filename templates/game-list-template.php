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
		$p1points = stripslashes_deep($_POST['player_1_score']) * 1000;
		$p2points = stripslashes_deep($_POST['player_2_score']) * 1000;
		$p3points = stripslashes_deep($_POST['player_3_score']) * 1000;
		$p4points = stripslashes_deep($_POST['player_4_score']) * 1000;
		$p1id = stripslashes_deep($_POST['player_1_id']);
		$p2id = stripslashes_deep($_POST['player_2_id']);
		$p3id = stripslashes_deep($_POST['player_3_id']);
		$p4id = stripslashes_deep($_POST['player_4_id']);
		$season = stripslashes_deep($_POST['season']);
		$wpdb->insert($mjdb->game_table, array(
			'time2' => $mjdb->get_datetime2_now(),
			'player_1_id' => $p1id,
			'player_2_id' => $p2id,
			'player_3_id' => $p3id,
			'player_4_id' => $p4id,
			'player_1_score' => $p1points,
			'player_2_score' => $p2points,
			'player_3_score' => $p3points,
			'player_4_score' => $p4points,
			'season' => $season
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
			?>
			<div class="row">
				<div class="col-md-6 row grid-spacing">
					<label class="col-xs-12">Player 1</label>
					<div class="col-xs-6">
						<select class="form-control" name="player_1_id"><?php mj_print_player_options() ?></select>
					</div>
					<div class="col-xs-6">
						<input class="form-control point-input" name="player_1_score" type="number" min="-200" max="200" step="0.1">
					</div>
				</div>
				<div class="col-md-6 row grid-spacing">
					<label class="col-xs-12">Player 2</label>
					<div class="col-xs-6">
						<select class="form-control" name="player_2_id"><?php mj_print_player_options() ?></select>
					</div>
					<div class="col-xs-6">
						<input class="form-control point-input" name="player_2_score" type="number" min="-200" max="200" step="0.1">
					</div>
				</div>
				<div class="col-md-6 row grid-spacing">
					<label class="col-xs-12">Player 3</label>
					<div class="col-xs-6">
						<select class="form-control" name="player_3_id"><?php mj_print_player_options() ?></select>
					</div>
					<div class="col-xs-6">
						<input class="form-control point-input" name="player_3_score" type="number" min="-200" max="200" step="0.1">
					</div>
				</div>
				<div class="col-md-6 row grid-spacing">
					<label class="col-xs-12">Player 4</label>
					<div class="col-xs-6">
						<select class="form-control" name="player_4_id"><?php mj_print_player_options() ?></select>
					</div>
					<div class="col-xs-6">
						<input class="form-control point-input" name="player_4_score" type="number" min="-200" max="200" step="0.1">
					</div>
				</div>
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
			<div class="row">
				<div class="col-xs-3 bottom-spaced">
					<div><p>Total:<span class="total">0</span></p></div>
				</div>
				<div class="col-xs-4 bottom-spaced form-group">
					<label class="control-label col-xs-3">Season:</label>
					<div class="col-xs-6">
						<select name="season" class="form-control">
							<?php
							$seasons = riichi_get_seasons();
							$current = riichi_get_season();
							foreach($seasons as $season){
								$name = riichi_get_season_name($season);
								if($season == $current){
									echo "<option selected value=\"$season\">$name</option>";
								} else {
									echo "<option value=\"$season\">$name</option>";
								}
							} ?>
						</select>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="controls"><button type="submit" class="btn btn-default">Create Game</button></div>
			</div>
		</fieldset>
	</form>
	<?php
}

get_footer();
?>