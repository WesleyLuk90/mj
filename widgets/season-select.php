<?php
class SeasonSelectorWidget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'season_select', // Base ID
			'Season Select', // Name
			array( 'description' => __( 'A widget to select the current season', 'riichi' ), ) // Args
			);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		$seasons = riichi_get_seasons();
		$current = riichi_get_season();
		?>
		<form class="select-season-form form-horizontal" role="form">
			<input type="hidden" name="action" value="select_season" />
			<div class="form-group">
				<label class="col-xs-2 control-label">Season:</label>
				<div class="col-xs-3">
					<select name="season" class="form-control">
						<?php 
						foreach($seasons as $season){
							$name = riichi_get_season_name($season);
							if($season == $current){
								echo "<option selected value=\"$season\">$name</option>";
							} else {
								echo "<option value=\"$season\">$name</option>";
							}
						} ?>
					</select>
					</div
				</div>
			</form>
			<?php
			echo $args['after_widget'];
		}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'text_domain' );
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}
}
add_action( 'widgets_init', function(){
	register_widget( 'SeasonSelectorWidget' );
});

add_action('wp_ajax_select_season', 'riichi_select_season');
add_action('wp_ajax_nopriv_select_season', 'riichi_select_season');
function riichi_select_season(){
	riichi_set_season($_POST['season']);
	echo "{}";
	die();
}
?>