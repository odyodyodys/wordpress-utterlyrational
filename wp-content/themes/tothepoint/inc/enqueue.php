<?php
/**
 * Understrap enqueue scripts
 *
 * @package understrap
 */

global $theme;

/**
 * Load theme's JavaScript sources.
 */
function understrap_scripts()
{
	// Get the theme data.
	$the_theme = wp_get_theme();
	wp_enqueue_style( 'understrap-styles', get_stylesheet_directory_uri() . '/css/theme.min.css', array(), $the_theme->get( 'Version' ) );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'understrap-scripts', get_template_directory_uri() . '/js/theme.min.js', array(), $the_theme->get( 'Version' ), true );
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
	{
		wp_enqueue_script( 'comment-reply' );
	}
	
	// about page scripts
	if(is_page($theme->aboutPageEngId)):	   
	   wp_enqueue_script('momentjs', 'https://momentjs.com/downloads/moment.min.js', array('jquery'));
	   wp_enqueue_script('chartjs', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js', array('momentjs'));
	   wp_enqueue_script('moment-range', 'https://cdnjs.cloudflare.com/ajax/libs/moment-range/3.0.3/moment-range.min.js', array('chartjs'));
	   wp_enqueue_script('about-page', get_template_directory_uri() . '/js/about.js', array('chartjs'));	   
	endif;
	
}
add_action( 'wp_enqueue_scripts', 'understrap_scripts' );
