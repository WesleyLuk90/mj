<?php
class UserDetails {
	public function __construct(){
		$this->isEditor = current_user_can('mj_edit_games');
		$this->userID = (int)stripslashes_deep($_GET['id']);
		$userinfo = get_userdata($this->userID);
		$this->username = $userinfo->display_name;
		$this->games = mj_get_games_of_player($this->userID);
	}

	public function getUsername(){
		return $this->username;
	}

	public function getGamesPlayed(){
		return count($this->games);
	}

	public function getPlacementTally(){
		$placements = array_map('mj_get_game_placement', $this->games);
		$tallies = mj_tally_player_placement($this->userID, $placements);
		$playernames = mj_get_players_names(mj_get_player_ids_from_games($this->games));

		foreach ($tallies as $playerid => &$tally) {
			$tally['playername'] = $playernames[$playerid];
		}

		return $tallies;
	}

	public function printGameTable(){
		mj_print_game_table(array(
			'games' => $this->games,
			'isEditor' => $this->isEditor
		));
	}
}
?>