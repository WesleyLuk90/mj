<?php

class MJDB {

	const GAME_FLAG_NORMAL = 0;
	const GAME_FLAG_DELETED = 1;
	const CURRENT_VERSION = "0.30";


	public function __construct(){
		$this->create_table_names();
		add_action( 'after_setup_theme', array($this, 'mj_check_install') );
	}

	public function create_table_names(){
		global $wpdb;
		
		$table_prefix = $wpdb->prefix;
		$this->game_table = "{$table_prefix}riichi_games";
	}

	public function update_databases(){
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$game_sql = "CREATE TABLE {$this->game_table} (
			id mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			time2 BIGINT DEFAULT 0 NOT NULL,
			season mediumint(9) NOT NULL DEFAULT 1
			player_1_id mediumint(9) NOT NULL,
			player_2_id mediumint(9) NOT NULL,
			player_3_id mediumint(9) NOT NULL,
			player_4_id mediumint(9) NOT NULL,
			player_1_score DOUBLE NOT NULL,
			player_2_score DOUBLE NOT NULL,
			player_3_score DOUBLE NOT NULL,
			player_4_score DOUBLE NOT NULL,
			flag mediumint(9) DEFAULT 0 NOT NULL,
			KEY player_1_id (player_1_id),
			KEY player_2_id (player_2_id),
			KEY player_3_id (player_3_id),
			KEY player_4_id (player_4_id)
		);";

		dbDelta($game_sql);

		$data = $wpdb->get_results("SELECT * FROM {$this->game_table}");
		foreach($data as $i => $value){
			// echo "DateTime:{$value->time}";
			$datetime = new DateTime($value->time);
			// print_r($datetime->getTimestamp());
			$update_data = array('time2' => $datetime->getTimestamp());
			$where = array('id' => $value->id);
			$wpdb->update($this->game_table, $update_data, $where);
			// echo "<br>";
		}
	}

	public function get_datetime_now(){
		return date ("Y-m-d H:i:s");
	}
	
	public function get_datetime2_now(){
		return time();
	}

	public function update_roles_capabilites(){
   		$role = get_role( 'administrator' );
   		$role->add_cap( 'mj_edit_games' ); 
	}

	public function mj_check_install(){
		if(get_option("mj_version_number") !== self::CURRENT_VERSION){
			$this->update_databases();
			$this->update_roles_capabilites();
			update_option("mj_version_number", self::CURRENT_VERSION);
		}
	}
}

$mjdb = new MJDB();
?>