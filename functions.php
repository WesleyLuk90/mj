<?php

require_once(get_stylesheet_directory() . "/riichi.php");

function mj_format_percent($num){
	return sprintf("%d%%", $num * 100);
}

function mj_format_score($score){
	return sprintf("%d", round($score));
}

function mj_enqueue_scripts() {
    wp_enqueue_script( 'jQuery', '//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js', null, '1.9.1', true );
    wp_enqueue_script( 'bootstrap',  get_stylesheet_directory_uri() . '/js/bootstrap.min.js', array('jQuery'), null, true );
}

add_action( 'wp_enqueue_scripts', 'mj_enqueue_scripts' );


function mj_wp_head(){
	echo '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />';
}

function mj_format_class($class){
	if($class){
		return "class=\"$class\"";
	}
	return "";
}

add_action( 'wp_head', 'mj_wp_head');
?>