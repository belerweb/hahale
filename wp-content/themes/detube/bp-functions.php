<?php
/**
 * BP Template Compat Functions
 *
 * Sets up the current WP theme for BuddyPress compatibility.
 * Most of these functions are extrapolated from bp-default's functions.php.
 *
 * @package deTube
 * @subpackage Functions
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// Check to make sure the active theme is not bp-default
if ( 'bp-default' == get_option( 'template' ) )
	return;

/**
 * Sets up WordPress theme for BuddyPress support.
 *
 * @since 1.3
 */
function bp_compat_theme_setup() {
	global $bp;

	// Load the default BuddyPress AJAX functions
	require_once( TEMPLATEPATH . '/bp/ajax.php' );
	
	/* Register Sidebars */
	register_sidebar(array(
		'name' => __('BuddyPress Sidebar', 'dp'),
		'id' => 'buddypress',
		'description' => __('This sidebar will displayed on BuddyPress pages.', 'dp'),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<div class="widget-header"><h3 class="widget-title">',
		'after_title' => '</h3></div>',
	));

	if ( !is_admin() ) {
		// Register buttons for the relevant component templates
		// Friends button
		if ( bp_is_active( 'friends' ) )
			add_action( 'bp_member_header_actions',    'bp_add_friend_button' );

		// Activity button
		if ( bp_is_active( 'activity' ) )
			add_action( 'bp_member_header_actions',    'bp_send_public_message_button' );

		// Messages button
		if ( bp_is_active( 'messages' ) )
			add_action( 'bp_member_header_actions',    'bp_send_private_message_button' );

		// Group buttons
		if ( bp_is_active( 'groups' ) ) {
			add_action( 'bp_group_header_actions',     'bp_group_join_button' );
			add_action( 'bp_group_header_actions',     'bp_group_new_topic_button' );
			add_action( 'bp_directory_groups_actions', 'bp_group_join_button' );
		}

		// Blog button
		if ( bp_is_active( 'blogs' ) )
			add_action( 'bp_directory_blogs_actions',  'bp_blogs_visit_blog_button' );
	}
}
add_action( 'after_setup_theme', 'bp_compat_theme_setup', 11 );

/**
 * Add html tags
 *
 * @since 1.3
 */
add_action( 'get_header', 'bp_compat_html' ); 
function bp_compat_html($name) {
	if($name == 'buddypress') {
		add_action('dp_after_header_php', 'bp_after_header_html');
		add_action('dp_before_footer_php', 'bp_before_footer_html');
	}
}
function bp_after_header_html() {
	echo '<div id="main"><div class="wrap cf">';
}
function bp_before_footer_html() {
	echo '</div></div><!-- end #main -->';
}

/**
 * Enqueues BuddyPress JS and related AJAX functions
 *
 * @since 1.3
 */
function bp_compat_enqueue_scripts() {

	// Add words that we need to use in JS to the end of the page so they can be translated and still used.
	$params = array(
		'my_favs'           => __( 'My Favorites', 'dp' ),
		'accepted'          => __( 'Accepted', 'dp' ),
		'rejected'          => __( 'Rejected', 'dp' ),
		'show_all_comments' => __( 'Show all comments for this thread', 'dp' ),
		'show_all'          => __( 'Show all', 'dp' ),
		'comments'          => __( 'comments', 'dp' ),
		'close'             => __( 'Close', 'dp' )
	);

	// BP 1.5+
	if ( version_compare( BP_VERSION, '1.3', '>' ) ) {
		// Bump this when changes are made to bust cache
		$version            = '20110818';

		$params['view']     = __( 'View', 'dp' );
	}
	// BP 1.2.x
	else {
		$version = '20110729';

		if ( bp_displayed_user_id() )
			$params['mention_explain'] = sprintf( __( "%s is a unique identifier for %s that you can type into any message on this site. %s will be sent a notification and a link to your message any time you use it.", 'dp' ), '@' . bp_get_displayed_user_username(), bp_get_user_firstname( bp_get_displayed_user_fullname() ), bp_get_user_firstname( bp_get_displayed_user_fullname() ) );
	}

	// Enqueue the global JS - Ajax will not work without it
	wp_enqueue_script( 'bp-theme-js', trailingslashit(get_template_directory_uri()).'bp/global.js', array( 'jquery' ), $version );

	// Localize the JS strings
	wp_localize_script( 'bp-theme-js', 'BP_Theme', $params );
}
add_action( 'wp_enqueue_scripts', 'bp_compat_enqueue_scripts' );

/**
 * Enqueues BuddyPress basic styles
 *
 * @since 1.3
 */
function bp_compat_enqueue_styles() {
	wp_enqueue_style( 'bp-style', trailingslashit(get_template_directory_uri()) . 'bp/bp-style.css', array());
}
add_action( 'wp_enqueue_scripts', 'bp_compat_enqueue_styles' );

if ( !function_exists( 'bp_tpack_use_wplogin' ) ) :
/**
 * BP Template Pack doesn't use bp-default's built-in sidebar login block,
 * so during no access requests, we need to redirect them to wp-login for
 * authentication.
 *
 * @since 1.3
 */
function bp_tpack_use_wplogin() {
	// returning 2 will automatically use wp-login
	return 2;
}
//add_filter( 'bp_no_access_mode', 'bp_tpack_use_wplogin' );
endif;

?>