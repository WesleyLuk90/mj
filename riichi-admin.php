<?php
class RiichiOptions{
	public function __construct(){
		if(is_admin()){
			add_action('admin_menu', array($this, 'add_plugin_page'));
			add_action('admin_init', array($this, 'page_init'));
		}
	}
	
	public function add_plugin_page(){
		// This page will be under "Settings"
		add_menu_page('Riichi Admin', 'Riichi', 'manage_options', 'riichi-options-page', array($this, 'create_admin_page'));
	}

	public function create_admin_page(){
		?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2>Settings</h2>			
		<form method="post" action="options.php">
			<?php
					// This prints out all hidden setting fields
			settings_fields('test_option_group');	
			do_settings_sections('riichi-options-page');
		?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
	}
	
	public function page_init(){		
		register_setting('test_option_group', 'riichi_game_detail_url');
		register_setting('test_option_group', 'riichi_user_detail_url');
		
		// Create a displayable section
		add_settings_section(
			'setting_section_id',
			'Setting',
			null,
			'riichi-options-page'
		);	
		// Add fields to that section
		add_settings_field(
			'game-details-page', 
			'Game Details Page', 
			array($this, 'create_game_detail_field'), 
			'riichi-options-page',
			'setting_section_id'			
		);		
		add_settings_field(
			'user-details-page', 
			'User Details Page', 
			array($this, 'create_user_detail_field'), 
			'riichi-options-page',
			'setting_section_id'			
		);		
	}
	
	public function create_game_detail_field(){
		$pages = get_pages();
		$gameDetailPage = get_option( "riichi_game_detail_url" );
		echo "<select name=\"riichi_game_detail_url\">";
		foreach ($pages as $key => $value) {
			$link = esc_attr(get_permalink($value));
			$selected = $link === $gameDetailPage ? "selected" : "";
			echo "<option value=\"$link\" $selected>$value->post_title</option>";
		}
		echo "</select>";
	}
	
	public function create_user_detail_field(){
		$pages = get_pages();
		$gameDetailPage = get_option( "riichi_user_detail_url" );
		echo "<select name=\"riichi_user_detail_url\">";
		foreach ($pages as $key => $value) {
			$link = esc_attr(get_permalink($value));
			$selected = $link === $gameDetailPage ? "selected" : "";
			echo "<option value=\"$link\" $selected>$value->post_title</option>";
		}
		echo "</select>";
	}
}

$RiichiOptions = new RiichiOptions();
?>