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
define( 'LMSLIDER_SLIDE_POST_TYPE',        "lmslider_slides" );
define( 'LMSLIDER_VERSION',                        '1.0' );
define( 'LM_ACTION',                  get_bloginfo( 'wpurl' ) . "/wp-admin/admin.php?page=" . basename( __FILE__ ) );
define( 'LM_PATH',                  get_bloginfo( 'wpurl' ) .'/wp-content/plugins' . "/" . basename( dirname( __FILE__ ) ));

add_action( 'admin_menu', 'lmslider_menu' );
add_action( 'wp_ajax_lmslider_add_slide', 'lmslider_add_slide' );
add_shortcode( 'lmslider', 'lmslider_display' );
add_action( 'admin_init', 'lmslider_addbuttons' );
 add_action( 'admin_footer', 'lmslider_tinymce_plugin_dialog' );

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

    $deck_data = array(
                    'post_content' => "",
                    'post_title' => $post_params['title'],
                    'post_status' => 'publish',
                    'comment_status' => "closed",
                    'ping_status' => "closed",
                    'post_type' => LMSLIDER_DECK_POST_TYPE
                );

    if(isset($post_params['deckid']))
    {
        $deck_id = $post_params['deckid'];
        $deck_data['ID'] = $deck_id;
        wp_update_post($deck_data);
    } else
    {
        $deck_id = wp_insert_post($deck_data);
    }

    /*
    $deck_id = wp_insert_post( array(
                    'post_content' => "",
                    'post_title' => $post_params['title'],
                    'post_status' => 'publish',
                    'comment_status' => "closed",
                    'ping_status' => "closed",
                    'post_type' => LMSLIDER_DECK_POST_TYPE
                ) );*/


    foreach ( (array) $post_params['slide'] as $slide ) {

                    $slide_data = array(
                        'post_content' => $slide['content'],
                        'post_title' => $slide['title'],
                        'post_status' => "publish",
                        'comment_status' => "closed",
                        'ping_status' => "closed",
                        'post_parent' => $deck_id,
                        'menu_order' => $slide['slide_order'],
                        'post_type' => LMSLIDER_SLIDE_POST_TYPE
                    );

                    if(isset($slide['id']))
                    {
                        $slide_id = $slide['id'];
                        $slide_data['ID'] = $slide_id;
                       wp_update_post($slide_data);
                    }else
                    {
                        $slide_id = wp_insert_post($slide_data);
                    }
                   /* $slide_id = wp_insert_post( array(
                        'post_content' => $slide['content'],
                        'post_title' => $slide['title'],
                        'post_status' => "publish",
                        'comment_status' => "closed",
                        'ping_status' => "closed",
                        'post_parent' => $deck_id,
                        'menu_order' => $slide['slide_order'],
                        'post_type' => LMSLIDER_SLIDE_POST_TYPE
                    ) );*/
                }

                return $deck_id ;
}

function lmslider_delete_deck($deckid){

    if(lmslider_delete_slides($deckid))
        wp_delete_post($deckid,true);

}

function lmslider_delete_slides($deckid,$slideid =null){

    $slides = lmslider_load_slides($deckid);
    $slidesids_to_delete = null;

    foreach($slides as $slide)
    {
        $slidesids_to_delete[] = $slide->ID;
    }


    if(isset($slideid) && in_array($slideid, $slidesids_to_delete))
        $slidesids_to_delete = array($slideid);

    if(isset($slidesids_to_delete))
    foreach($slidesids_to_delete as $slideid_to_delete)
    {
        wp_delete_post($slideid_to_delete,true);
    }

    return true;    

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
    return $slides->posts;

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
    //die(var_dump($decks->posts[0]->post_title));
    return $decks;
}



function lmslider_action() {

	$currentAction = null;
    $lmslider_deck_id = null;
    $slides = array('null');

	if ( isset( $_GET['action'] ) && !empty( $_GET['action'] ) ) {
        $currentAction = $_GET['action'];

    }

    if (isset($_POST['action'])) {
        $currentAction = $_POST['action'];
        $lmslider_deck_id = lmslider_save_deck($_POST);
    }

    if ( isset( $_GET['id'] ) && !empty( $_GET['id'] ) ) {
        $lmslider_deck_id = intval($_GET['id']);
    }
    

    switch($currentAction){
    	case "edit" :
    	             if ( isset($lmslider_deck_id)) {
        		        $lmsliderdeck = lmslider_load_decks( $lmslider_deck_id );
                        $slides = lmslider_load_slides ($lmslider_deck_id);
    			     }

    	case "new" :

    		include( lmslider_dir( '/views/edit-deck.php' ) );
    		break;

        case "delete":
                    if(isset($_GET['deckid']))
                    {
                        $deckid = $_GET['deckid'];

                        if(isset($_GET['slideid']))
                        {
                            $slideid = $_GET['slideid'];
                            lmslider_delete_deck($deckid,$slideid);
                        }else
                        {
                            lmslider_delete_deck($deckid);
                        }
                            
                    }
                    
    	default:
    		$decks = lmslider_load_decks();
    		include( lmslider_dir( '/views/overview.php' ) );
    }



}

function lmslider_add_slide() {
    
    $count = $_POST['count'] + 1;    
    include( lmslider_dir( '/views/_edit-slide.php' ) );

    wp_die();
}

function lmslider_menu() {
    add_menu_page( 'LmSlider', 'LmSlider', 'publish_posts', basename( __FILE__ ), 'lmslider_action' );
}

function lmslider_display($atts){
    wp_register_script( 'mixit-library-js', LM_PATH . '/lib/jquery.mixitup.min.js' , array( 'jquery' ), LMSLIDER_VERSION );
    wp_register_script( 'animation-js', LM_PATH .  '/lib/animation.js' , array( 'jquery','mixit-library-js' ), LMSLIDER_VERSION );
    wp_register_style( 'lmslider-css', LM_PATH .'/lib/lmslider.css' , array(), LMSLIDER_VERSION, "screen" );
    wp_enqueue_style( 'lmslider-css' );

    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'mixit-library-js' );
    wp_enqueue_script( 'animation-js' );

    $slides = lmslider_load_slides($atts['deckid']);

    $template = "";

    $template .= '<div id="Container">';
    $count = 1;
    foreach((array) $slides as $slide )
    {
        $template .= '<div class="mix slide-'.$count.'">';
        $template .= $slide->post_content;
        $template .= '</div>';
        $count ++;
    }
            
    $template .= '</div>';

    return $template;


}

function lmslider_tinymce_plugin_dialog() {
    // Only load the necessary scripts and render the modal window dialog box if the user is on the post/page editing admin pages
    if ( in_array( basename( $_SERVER['PHP_SELF'] ), array( 'post-new.php', 'page-new.php', 'post.php', 'page.php' ) ) ) {
        $decks = lmslider_load_decks();
        
        include( lmslider_dir('/views/_tinymce-plugin-dialog.php'));
    }
}

function lmslider_addbuttons() {

    // Setup the stylesheet to use for the modal window interaction
    wp_register_style( 'popup-styles', LM_PATH.'/lib/popup.css'  );

    // Return false if the user does not have WYSIWYG editing privileges
    if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
        return false;
    }

    // Add buttons to TinyMCE editor if user can edit with WYSIWYG editor
    
    if ( 'true' == get_user_option( 'rich_editing' ) ) {
        
        add_filter( 'mce_external_plugins', 'lmslider_add_tinymce_plugin' );
        add_filter( 'mce_buttons', 'lmslider_register_button' );
    }

    // Only load the necessary scripts if the user is on the post/page editing admin pages
    if ( in_array( basename( $_SERVER['PHP_SELF'] ), array( 'post-new.php', 'page-new.php', 'post.php', 'page.php' ) ) ) {
        wp_enqueue_script( 'jquery-ui-dialog' );
        wp_enqueue_script( 'lmslider-popup', LM_PATH.'/lib/popup.js' , array('jquery-ui-dialog'), LMSLIDER_VERSION, true );
        wp_enqueue_style( 'popup-styles' );
    }
}

function lmslider_add_tinymce_plugin( $plugin_array ) {
    if(!lmslider_is_plugin())
        $plugin_array['lmslider'] = LM_PATH.'/lib/editor-plugin.js';
    

    return $plugin_array;
}

function lmslider_register_button( $buttons ) {
    array_push( $buttons, "separator", "lmslider" );
    return $buttons;
}

function lmslider_is_plugin() {
    return (boolean) ( ( "admin.php" == basename( $_SERVER['PHP_SELF'] ) ) && ( strpos( $_GET['page'], basename( __FILE__ ) ) !== false ) );
}