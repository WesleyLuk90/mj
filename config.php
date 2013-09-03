<?php
	function riichi_get_seasons(){
		return array(0, 1,2,3);
	}

	function riichi_get_season_name($number){
		if($number == 0){
			return "Overall";
		} else {
			return "Season $number";
		}
	}

	function riichi_get_default_season(){
		return 0;
	}
	// $wpdb->show_errors();
?>