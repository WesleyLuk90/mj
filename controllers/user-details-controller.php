<?php
class UserDetails {
	public function __construct(){
		$this->isEditor = current_user_can('mj_edit_games');
		$this->userID = (int)stripslashes_deep($_GET['id']);
		$userinfo = get_userdata($this->userID);
		$this->username = $userinfo->display_name;
		$this->games = mj_get_games_of_player($this->userID);

		$this->calculateTally();
		$this->calculateRunningAverage();
	}

	private function calculateRunningAverage(){
		$count = 0;
		$total = 0;
		$userid = $this->userID;
		$averages = array();
		foreach ($this->games as $key => $game) {
			$score = 0;
			if($game->player_1_id == $userid){
				$score = $game->player_1_score;
			} else if($game->player_2_id == $userid){
				$score = $game->player_2_score;
			} else if($game->player_3_id == $userid){
				$score = $game->player_3_score;
			} else if($game->player_4_id == $userid){
				$score = $game->player_4_score;
			}
			$total += $score;
			$count ++;
			$averages[] = array(
				'score' => $score,
				'average' => $total / $count,
			);
		}
		$this->averages = $averages;
	}

	private function calculateTally(){
		$placements = array_map('mj_get_game_placement', $this->games);
		$tallies = mj_tally_player_placement($this->userID, $placements);
		$playernames = mj_get_players_names(mj_get_player_ids_from_games($this->games));

		$this->tally = array();
		$this->playerTally = null;

		foreach ($tallies as $playerid => $tally) {
			if($playerid == $this->userID){
				$this->playerTally = $tally;
			} else {
				$tally['playername'] = $playernames[$playerid];
				$tally['playerid'] = $playerid;
				$this->tally[] = $tally;
			}
		}
	}

	public function getUsername(){
		return $this->username;
	}

	public function getGamesPlayed(){
		return count($this->games);
	}

	public function getTallyCount(){
		return count($this->tally);
	}

	public function getTallyPlayerName($i){
		return $this->tally[$i]['playername'];
	}

	public function getTallyPlayerID($i){
		return $this->tally[$i]['playerid'];
	}

	public function getTallyFirsts($i){
		return $this->tally[$i]['first'] / $this->tally[$i]['count'];
	}

	public function getTallySeconds($i){
		return $this->tally[$i]['second'] / $this->tally[$i]['count'];
	}

	public function getTallyThirds($i){
		return $this->tally[$i]['third'] / $this->tally[$i]['count'];
	}

	public function getTallyFourths($i){
		return $this->tally[$i]['forth'] / $this->tally[$i]['count'];
	}

	public function getTallyGameCount($i){
		return $this->tally[$i]['count'];
	}

	public function getPlayerFirsts(){
		return $this->playerTally['first'];
	}

	public function getPlayerSeconds(){
		return $this->playerTally['second'];
	}

	public function getPlayerThirds(){
		return $this->playerTally['third'];
	}

	public function getPlayerForths(){
		return $this->playerTally['forth'];
	}

	public function printGameTable(){
		mj_print_game_table(array(
			'games' => $this->games,
			'isEditor' => $this->isEditor
		));
	}

	public function getPlayerRunningAverage(){
		return $this->averages;
	}
}
?>