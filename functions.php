<?php

function my_scripts_method() {
    wp_enqueue_script( 'jQuery', '//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js', null, '1.9.1', true );
    wp_enqueue_script( 'bootstrap',  get_stylesheet_directory_uri() . '/js/bootstrap.min.js', array('jQuery'), null, true );
}

add_action( 'wp_enqueue_scripts', 'my_scripts_method' );

?>