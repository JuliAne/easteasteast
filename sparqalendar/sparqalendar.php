<?php
/*
Plugin Name: SPARQalendar - A SPARQL driven Calendar
Version: 0.0.1
Description: display Date Entities (like Events) on a Calendar using a dynamic SPARQL query to retrieve them
Author: leipzig-data.de
Author URI: http://leipzig-data.de
*/

include_once("kalender.php");
include_once("rdfmapping.php");




	//wp_enqueue_style( 'sparqalendar', plugins_url().'/sparqalendar/styles.css' );


add_action( 'init', 'register_sparqalendar_style' );
add_action( 'init', 'enqueue_sparqalendar_style' );
add_shortcode( 'sparqalendar', 'displayKalenderGet' );

/**
 * Register style sheet.
 */
function register_sparqalendar_style() {
	wp_register_style( 'sparqalendar', plugins_url( 'sparqalendar/styles.css' ) );
}
/**
 * enqueue style sheet.
 */
function enqueue_sparqalendar_style() {
	wp_enqueue_style( 'sparqalendar' );
	//echo plugins_url( 'sparqalendar/styles.css' );
}

	

?>