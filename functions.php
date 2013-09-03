<?php

require_once(get_stylesheet_directory() . "/config.php");
require_once(get_stylesheet_directory() . "/riichi.php");
require_once(get_stylesheet_directory() . "/widgets/season-select.php");

function mj_format_percent($num){
	return sprintf("%d%%", $num * 100);
}

function mj_format_score($score){
	return sprintf("%d", round($score));
}

function mj_enqueue_scripts() {
    wp_enqueue_script( 'jQuery', '//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js', null, '1.9.1', true );
    wp_enqueue_script( 'jsapi', 'https://www.google.com/jsapi', array('jQuery'), '1.9.1', true );
    wp_enqueue_script( 'bootstrap',  get_stylesheet_directory_uri() . '/js/bootstrap.min.js', array('jQuery'), null, true );
    wp_enqueue_script( 'script',  get_stylesheet_directory_uri() . '/js/script.js', array('jsapi'), null, true );

    wp_enqueue_style( 'style', get_stylesheet_directory_uri() . '/style.css');
    wp_enqueue_style( 'bootstrap', get_stylesheet_directory_uri() . '/css/bootstrap.min.css');

    wp_localize_script( 'script', 'riichi',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' )) );
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

register_nav_menu('primary', 'The primary menu');
?>