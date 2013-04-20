<?php
/*
Template Name: Hand Calculator
*/
?>
<?php
wp_enqueue_script( 'riichi-library',  get_stylesheet_directory_uri() . '/js/riichi-library/RiichiLibrary.js', null, true );
wp_enqueue_script( 'hand-calculator',  get_stylesheet_directory_uri() . '/js/HandCalculator.js', array('riichi-library', 'jQuery'), null, true );
wp_enqueue_style( 'hand-calculator-style', get_stylesheet_directory_uri() . '/css/HandCalculator.css' );
?>
<?php get_header(); ?>
<script type="text/javascript">
	var templateDir = "<?php echo get_stylesheet_directory_uri() ?>";
</script>
<div class="hand-calculator"></div>
<?php get_footer(); ?>