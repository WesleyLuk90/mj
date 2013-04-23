<?php

require_once(get_stylesheet_directory() . "/install.php");
require_once(get_stylesheet_directory() . "/riichi-admin.php");

function mj_get_games_list(){
	global $wpdb, $mjdb;
	return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$mjdb->game_table} WHERE flag = %d", $mjdb::GAME_FLAG_NORMAL));
}

function mj_get_games_of_player($userid){
	global $wpdb, $mjdb;
	return $wpdb->get_results($wpdb->prepare("
		SELECT * FROM {$mjdb->game_table}
		WHERE flag = %d AND (
			player_1_id = %d OR
			player_2_id = %d OR
			player_3_id = %d OR
			player_4_id = %d
		)", $mjdb::GAME_FLAG_NORMAL, $userid, $userid, $userid, $userid));
}

function mj_tally_player_placement($userid, $game_placement_array){
	// Assumes that $userid occurs once in each $game_placement_array
	$players = array();
	$our_placement = array();
	// Get a list of all players
	foreach ($game_placement_array as $gameindex => $game) {
		foreach ($game['players'] as $placement => $playerid){
			if($playerid == $userid){
				$our_placement[$gameindex] = $placement;
				continue;
			}
			$players[$playerid] = array(
				'first' => 0,
				'second' => 0,
				'third' => 0,
				'forth' => 0,
				'count' => 0
			);
		}
	}
	foreach ($game_placement_array as $gameindex => $game) {
		foreach ($game['players'] as $placement => $playerid){
			if($playerid == $userid){
				continue;
			}
			$this_game_placement = $our_placement[$gameindex];
			switch($this_game_placement){
				case 0:
					$players[$playerid]['first']++;
					break;
				case 1:
					$players[$playerid]['second']++;
					break;
				case 2:
					$players[$playerid]['third']++;
					break;
				case 3:
					$players[$playerid]['forth']++;
					break;
			}
			$players[$playerid]['count']++;
		}
	}
	return $players;
}

function mj_get_game_placement($game){
	$users = array();
	$points = array();
	$users[] = $game->player_1_id;
	$users[] = $game->player_2_id;
	$users[] = $game->player_3_id;
	$users[] = $game->player_4_id;
	$points[] = $game->player_1_score;
	$points[] = $game->player_2_score;
	$points[] = $game->player_3_score;
	$points[] = $game->player_4_score;

	array_multisort($points, SORT_DESC, $users);

	return array(
		'players' => $users,
		'score' => $points
	);
}

function mj_get_player_list(){
	return get_users(array('role'=>'subscriber'));
}

function mj_get_players_names($ids){
	$player_ids = array_unique($ids);
	$users = get_users(array('include' => $player_ids));
	$player_names = array();
	foreach ($users as $key => $user) {
		$player_names[$user->ID] = $user->display_name;
	}
	return $player_names;
}

function mj_get_player_ids_from_games($games){
	$player_ids = array();
	foreach ($games as $key => $row) {
		$player_ids[] = $row->player_1_id;
		$player_ids[] = $row->player_2_id;
		$player_ids[] = $row->player_3_id;
		$player_ids[] = $row->player_4_id;
	}
	return $player_ids;
}

function mj_print_player_select( $args = array() ){
	$defaults = array(
		'name' => "player_select",
		'class' => "",
		'label' => "Player Select"
	);
	$args = wp_parse_args($args, $defaults);
	extract($args, EXTR_SKIP);

	$users = mj_get_player_list();

	$class = mj_format_class($class);

	echo '<div class="control-group">';
	echo '<label class="control-label">';
	echo esc_html($label);
	echo '</label>';
	echo '<div class="controls">';
	echo "<select $class name=\"$name\">";
	foreach ($users as $key => $value) {
		echo "<option value=\"{$value->ID}\">{$value->user_firstname}</option>";
	}
	echo "</select>";
	echo '</div>';
	echo "</div>";
}

function mj_print_point_input_box( $args = array() ){
	$defaults = array(
		'name' => 'point_input',
		'class' => "input-mini",
		'label' => ""
	);

	$args = wp_parse_args($args, $defaults);
	extract($args, EXTR_SKIP);

	$class = mj_format_class($class);

	echo '<div class="control-group">';
	echo '<label class="control-label">';
	echo esc_html($label);
	echo '</label>';
	echo '<div class="controls">';
	echo '<div class="input-append">';
	echo "<input type=\"text\" name=\"$name\" $class>";
	echo '<span class="add-on">K</span>';
	echo '</div>';
	echo '</div>';
	echo "</div>";
}


function mj_get_current_page_url(){
	return "http://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
}

function mj_get_user_detail_page_url($userid){
	return sprintf('%s?id=%d', get_option('riichi_user_detail_url'), $userid);
}

function mj_print_game_table($args){
	global $gameData, $isEditor;
	$defaults = array(
		'games' => array(),
		'isEditor' => false
	);

	$args = wp_parse_args($args, $defaults);
	extract($args);

	$player_ids = mj_get_player_ids_from_games($games);
	$player_names = mj_get_players_names($player_ids);

	$gameData = array();
	foreach ($games as $key => $game) {
		$scores = array();
		$ids = array();
		$names = array();

		$ids[] = $game->player_1_id;
		$scores[] = $game->player_1_score;
		$names[] = $player_names[$game->player_1_id];

		$ids[] = $game->player_2_id;
		$scores[] = $game->player_2_score;
		$names[] = $player_names[$game->player_2_id];

		$ids[] = $game->player_3_id;
		$scores[] = $game->player_3_score;
		$names[] = $player_names[$game->player_3_id];

		$ids[] = $game->player_4_id;
		$scores[] = $game->player_4_score;
		$names[] = $player_names[$game->player_4_id];

		array_multisort($scores, SORT_DESC, $ids, $names);
		$gameData[] = array(
			'scores' => $scores,
			'ids' => $ids,
			'names' => $names,
			'time' => $game->time,
			'game_id' => $game->id
		);
	}

	get_template_part('mj', 'gametable');
}
?>