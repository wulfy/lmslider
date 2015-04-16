<?php
/**
 * Plugin Name: lmslider
 * Plugin URI: 
 * Description: Slider for lm securite website
 * Version: 1.0.0
 * Author: LLA
 * Author URI: 
 * License: GPL2
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
define( 'LM_TABLE',                         $wpdb->prefix . "lmslider_decks" );
define( 'LM_SLIDES_TABLE',                  $wpdb->prefix . "lmslider_slides" );
define( 'LMSLIDER_DECK_POST_TYPE',          "lmslider_decks" );
define( 'LMSLIDER_SLIDES_POST_TYPE',        "lmslider_slides" );

define( 'LM_ACTION',                  get_bloginfo( 'wpurl' ) . "/wp-admin/admin.php?page=" . basename( __FILE__ ) . "/lmslider_action" );


add_action( 'admin_menu', 'lmslider_menu' );

function lmslider_install() {
	global $wpdb;

   /*$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE ".LM_TABLE." (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
	  name tinytext NOT NULL,
	  UNIQUE KEY id (id)
	) $charset_collate;";

	$sql2 = "CREATE TABLE ".LM_SLIDES_TABLE." (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
	  text text NOT NULL,
	  deckid mediumint(9),
	  UNIQUE KEY id (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );


	dbDelta( $sql );
	dbDelta( $sql2 );*/
}

function lmslider_dir( $str="" ) {
    $path =  WP_PLUGIN_DIR . "/" . basename( dirname( __FILE__ ) );
    
    if ( isset( $str ) && !empty( $str ) ) {
        $sep = "/" == substr( $str, 0, 1 ) ? "" : "/";
        return $path . $sep . $str;
    } else {
        return $path;
    }
}

function lmslider_list(){
	$decks = lmslider_load_decks();
	include( lmslider_dir( '/views/overview.php' ) );

}

function lmslider_save_deck($post_params = null){

	if( !isset( $post_params ) ) {
        return false;
    }

    $deck_id = wp_insert_post( array(
                    'post_content' => "",
                    'post_title' => $post_params['title'],
                    'post_status' => 'publish',
                    'comment_status' => "closed",
                    'ping_status' => "closed",
                    'post_type' => SLIDEDECK_POST_TYPE
                ) );


    foreach ( (array) $params['slide'] as $slide ) {
                    $slide_id = wp_insert_post( array(
                        'post_content' => $slide['content'],
                        'post_title' => $slide['title'],
                        'post_status' => "publish",
                        'comment_status' => "closed",
                        'ping_status' => "closed",
                        'post_parent' => $deck_id,
                        'menu_order' => $slide['slide_order'],
                        'post_type' => SLIDEDECK_SLIDE_POST_TYPE
                    ) );
                }


}



function lmslider_load_slides ($deckid){

$query_params = array(
        'post_type' => LMSLIDER_SLIDE_POST_TYPE,
        'post_parent' => $deckid,
        'posts_per_page' => -1,
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'post__not_in' => get_option( 'sticky_posts' )
    );

    $slides = new WP_Query( $query_params );
    return $slides;

}

function lmslider_load_decks($deckid = null) {

	$query_params = array(
        'post_type' => LMSLIDER_DECK_POST_TYPE,
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
        'post__not_in' => get_option( 'sticky_posts' )
    );

	if($deckid != null)
		$query_params['p'] = $deckid;

    $decks = new WP_Query( $query_params );
    return $decks;
}



function lmslider_action() {

	$currentAction = null;
	if ( isset( $_GET['action'] ) && !empty( $_GET['action'] ) ) {
        $currentAction = $_GET['action'];
    }

    switch($currentAction){

    	case "edit" :
    	    if ( isset( $_GET['id'] ) && !empty( $_GET['id'] ) ) {
        		$lmslider_deck_id = intval( $_GET['id'] );
        		$lmsliderdeck = lmsliderdeck_load( $lmslider_deck_id );
    			}
    	case "new" :
    		include( lmslider_dir( '/views/edit-deck.php' ) );
    		break;

    	default:
    		$decks = lmslider_load_decks();
    		include( lmslider_dir( '/views/overview.php' ) );
    }



}

function lmslider_menu() {
    add_menu_page( 'LmSlider', 'LmSlider', 'publish_posts', basename( __FILE__ ), 'lmslider_list' );
}


