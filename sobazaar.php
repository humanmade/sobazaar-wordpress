<?php
/*
Plugin Name: SOBAZAAR
Plugin URI: http://wordpress.org/extend/plugins/sobazaar/
Version: 1.0
Author: Alice Cyan Carlsson
Description: Show off your fashion boards.
Domain Path: /languages
Text Domain: sobazaar-wordpress
*/

/* Some configuration */
define('SOBAZAAR_EMBED_SRV', 'http://alice.sobazaar.com/'); // Test server - Do not use in production!

// Load plugin textdomain.
function sobazaar_load_textdomain() {
	load_plugin_textdomain( 'sobazaar-wordpress', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}
add_action( 'plugins_loaded', 'sobazaar_load_textdomain' );

/*
 *  Part one of this plugin is the meta box shown in admin when editing a post
 */

/* Register style sheet for admin */
function sobazaar_register_admin_styles() {
	wp_register_style( 'sobazaar', plugins_url( 'sobazaar/css/sobazaar-admin.css' ) );
	wp_enqueue_style( 'sobazaar' );
}
add_action( 'admin_enqueue_scripts', 'sobazaar_register_admin_styles' );

/* Meta box setup function. */
function sobazaar_post_meta_boxes_setup() {

	/* Add meta boxes on the 'add_meta_boxes' hook. */
	add_action( 'add_meta_boxes', 'sobazaar_add_post_meta_boxes' );
}

/* Fire our meta box setup function on the post editor screen. */
add_action( 'load-post.php', 'sobazaar_post_meta_boxes_setup' );
add_action( 'load-post-new.php', 'sobazaar_post_meta_boxes_setup' );

/* Create one or more meta boxes to be displayed on the post editor screen. */
function sobazaar_add_post_meta_boxes() {
	add_meta_box(
		'sobazaar-board', 				// Unique ID
		esc_html__( 'SOBAZAAR', 'sobazaar-wordpress' ),	// Title
		'sobazaar_board_meta_box',		// Callback function
		'post',							// Admin page (or post type)
		'side',							// Context
		'default'						// Priority
	);
}

/* Display the post meta box. */
function sobazaar_board_meta_box( $object, $box ) { ?>

	<?php wp_nonce_field( basename( __FILE__ ), 'sobazaar_board_nonce' ); ?>

	<p>
		<label for="sobazaar-board"><?php _e( "Find the board you want below, then click on 'Get shortcode'. ", 'sobazaar-wordpress' ); ?></label>
		<br />
		<iframe class="widefat sobazaar-admin-iframe" src="<?php echo esc_url( SOBAZAAR_EMBED_SRV ); ?>"></iframe>
	</p>
	
<?php }

/*
 *  Part two is registering a shortcode for displaying fashion boards by use of an iframe
 */

/* Register style sheet */
function sobazaar_register_styles() {
	wp_register_style( 'sobazaar', plugins_url( 'sobazaar/css/sobazaar.css' ) );
	wp_enqueue_style( 'sobazaar' );
}
add_action( 'wp_enqueue_scripts', 'sobazaar_register_styles' );

/* Register the shortcode */
function sobazaar_shortcode_function( $atts ) {
	
	extract( shortcode_atts(array(
		'board' => 0,
	), $atts) );

	if ( ! $board ) {
		return false;
	}
	
	else return '<div class="sobazaar-iframe-wrapper"><iframe class="widefat sobazaar-iframe" src="http://alice.sobazaar.com/#/board/58-' . esc_url( $board ) . '"></iframe></div>';
	
}

function sobazaar_register_shortcodes(){
	add_shortcode( 'sobazaar', 'sobazaar_shortcode_function' );
}
add_action( 'init', 'sobazaar_register_shortcodes' );
