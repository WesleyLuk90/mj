<?php
class UserList {
	public function __construct(){
		$this->games = mj_get_games_list();
		$this->player_ids = mj_get_player_ids_from_games($this->games);
		$this->player_names = mj_get_players_names($this->player_ids);
		$this->calculatePlayersScore();
	}

	public function calculatePlayersScore(){
		$scores = array();
		$totals = array();
		$count = array();
		foreach ($this->games as $key => $game) {
			$p1 = $game->player_1_id;
			$p2 = $game->player_2_id;
			$p3 = $game->player_3_id;
			$p4 = $game->player_4_id;
			$s1 = $game->player_1_score;
			$s2 = $game->player_2_score;
			$s3 = $game->player_3_score;
			$s4 = $game->player_4_score;

			if(array_key_exists($p1, $totals)){
				$totals[$p1] += $s1;
				$count[$p1]++;
			} else {
				$totals[$p1] = $s1;
				$count[$p1] = 1;
			}
			if(array_key_exists($p2, $totals)){
				$totals[$p2] += $s2;
				$count[$p2]++;
			} else {
				$totals[$p2] = $s2;
				$count[$p2] = 1;
			}
			if(array_key_exists($p3, $totals)){
				$totals[$p3] += $s3;
				$count[$p3]++;
			} else {
				$totals[$p3] = $s3;
				$count[$p3] = 1;
			}
			if(array_key_exists($p4, $totals)){
				$totals[$p4] += $s4;
				$count[$p4]++;
			} else {
				$totals[$p4] = $s4;
				$count[$p4] = 1;
			}
		}
		$averages = array();
		foreach ($totals as $playerid => $total) {
			$averages[$playerid] = $total / $count[$playerid];
		}
		$scores['average'] = $averages;
		$this->scores = $scores;
	}

	public function getPlayerCount(){
		return count($this->player_ids);
	}

	public function getPlayerName($i){
		return $this->player_names[$this->player_ids[$i]];
	}

	public function getPlayerID($i){
		return $this->player_ids[$i];
	}

	public function getPlayerAverage($i){
		return $this->scores['average'][$this->player_ids[$i]];
	}

} 
?>